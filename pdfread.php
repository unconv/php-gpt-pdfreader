<?php
function pdf_to_text( $filename ) {
    $temp_file = tempnam( "/tmp", "PDFTOTEXT" );
    exec( "pdftotext " . escapeshellarg( $filename ) . " " . escapeshellarg( $temp_file ) );

    register_shutdown_function(function() use ( $temp_file ) {
        @unlink($temp_file);
    });

    return $temp_file;
}

function chunk_text_file( $filename, $chunk_size = 4000, $overlap = 1000 ) {
    $chunks = [];

    $file = fopen( $filename, "r" );
    while( ! feof( $file ) ) {
        $chunk = fread( $file, $chunk_size );
        $chunks[] = $chunk;
        if( ftell( $file ) >= $overlap && strlen( $chunk ) >= $chunk_size ) {
            fseek( $file, -$overlap, SEEK_CUR );
        }
    }

    return $chunks;
}
