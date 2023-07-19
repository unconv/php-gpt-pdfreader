<?php
require("gpt.php");
require("lookup.php");
require("pdfread.php");

echo "GPT: What do you want to know?\nYou: ";
$stdin = fopen("php://stdin", "r");
$question = trim(fgets($stdin));
fclose( $stdin );
echo "\n";

$keywords = get_keywords( $question );

echo "Searching for: " . implode( ", ", $keywords ) . "\n\n";

$text_file = pdf_to_text( "4hours.pdf" );
$chunks = chunk_text_file( $text_file );
$matches = find_matches( $chunks, $keywords );

foreach( $matches as $chunk_id => $points ) {
    $answer = answer_question( $chunks[$chunk_id], $question );

    if( isset( $answer->name ) && $answer->name == "give_response" ) {
        $arguments = json_decode( $answer->arguments, true );
        $response = $arguments["response"];
        echo "\nGPT: " . $response . "\n";
        break;
    }
}

if( ! isset( $answer->name ) || $answer->name != "give_response" ) {
    echo "\nGPT: I can't find the answer\n";
}

