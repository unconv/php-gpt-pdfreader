<?php
require("gpt.php");
require("lookup.php");
require("pdfread.php");

function input( $question, $end = "" ) {
    echo $question;

    $stdin = fopen( "php://stdin", "r" );
    $question = trim( fgets( $stdin ) );
    fclose( $stdin );

    echo $end;

    return $question;
}

if( ! getenv( "OPENAI_API_KEY" ) ) {
    $api_key = input( "Please enter your OpenAI API key: ", "\n" );
    putenv( "OPENAI_API_KEY=" . $api_key );
}

while( ! isset( $filename ) || ! file_exists( $filename ) ) {
    $filename = input( "Please enter a filename to read: " );
    if( ! file_exists( $filename ) ) {
        echo "ERROR: File does not exist!\n\n";
    }
}

echo "\nChunking PDF file...\n\n";
$text_file = pdf_to_text( $filename );
$chunks = chunk_text_file( $text_file );

while( true ) {
    $question = input( "GPT: What do you want to know?\nYou: ", "\n" );

    $keywords = get_keywords( $question );

    echo "Searching for: " . implode( ", ", $keywords ) . "\n\n";

    $matches = find_matches( $chunks, $keywords );

    foreach( $matches as $chunk_id => $points ) {
        $answer = answer_question( $chunks[$chunk_id], $question );

        if( $answer !== false ) {
            echo "\n\nGPT: " . $answer->content . "\n\n";
            break;
        }
    }

    if( $answer === false ) {
        echo "\n\nGPT: I can't find the answer\n\n";
    }
}
