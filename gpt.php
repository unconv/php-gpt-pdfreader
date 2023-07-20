<?php
require("ChatGPT.php");

/**
 * List keywords to the user
 *
 * @param array<string> $keywords A list of keywords
 */
function list_keywords( $keywords ) {

}

/**
 * Call this function if the answer is not found
 *
 * @param bool $not_found
 */
function answer_not_found( bool $not_found = true ) {

}

function get_keywords( string $question ) {
    $prompt = "I want to search for the answer to this question from a PDF file. Please give me a list of keywords that I could use to search for the information.

```
$question
```

Use the list_keywords function to respond.";

    $chatgpt = new ChatGPT( getenv("OPENAI_API_KEY") );
    $chatgpt->add_function( "list_keywords" );
    $chatgpt->smessage( "You a are a search keyword generator" );
    $chatgpt->umessage( $prompt );

    $response = $chatgpt->response( raw_function_response: true );
    $function_call = $response->function_call;

    $arguments = json_decode( $function_call->arguments, true );
    $keywords = strtolower( implode( " ", $arguments["keywords"] ) );
    $keywords = explode( " ", $keywords );

    return $keywords;
}

function answer_question( string $chunk, string $question ) {
    echo ".";

    $chatgpt = new ChatGPT( getenv("OPENAI_API_KEY") );
    $chatgpt->add_function( "answer_not_found" );
    $chatgpt->smessage( "The user will give you an excerpt from PDF file. Answer the question based on the information in the excerpt. If the answer can not be determined from the excerpt, call the answer_not_found function." );
    $chatgpt->umessage( "### EXCERPT FROM PDF:\n\n$chunk" );
    $chatgpt->umessage( $question );

    $response = $chatgpt->response( raw_function_response: true );

    if( isset( $response->function_call ) ) {
        return false;
    }

    if( empty( $response->content ) ) {
        return false;
    }

    if( $chatgpt->version() < 4 && ! gpt3_check( $question, $response->content ) ) {
        return false;
    }

    return $response;
}

function gpt3_check( $question, $answer ) {
    $chatgpt = new ChatGPT( getenv("OPENAI_API_KEY") );
    $chatgpt->umessage( "Question: \"$question\"\nAnswer: \"$answer\"\n\nAnswer YES if the answer is similar to 'the answer to the question was not found in the information provided' or 'the excerpt does not mention that'. Answer only YES or NO" );
    $response = $chatgpt->response();

    return stripos( $response->content, "yes" ) === false;
}
