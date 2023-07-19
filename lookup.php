<?php
function find_matches( array $chunks, array $keywords ) {
    $df = [];

    foreach( $chunks as $chunk_id => $chunk ) {
        foreach( $keywords as $keyword ) {
            $chunk = strtolower( $chunk );
            $chunks[$chunk_id] = $chunk;
            $occurences = substr_count( $chunk, $keyword );
            if( ! isset( $df[$keyword] ) ) {
                $df[$keyword] = 0;
            }
            $df[$keyword] += $occurences;
        }
    }

    $results = [];

    foreach( $chunks as $chunk_id => $chunk ) {
        foreach( $keywords as $keyword ) {
            $occurences = substr_count( $chunk, $keyword );
            if( ! isset( $results[$chunk_id] ) ) {
                $results[$chunk_id] = 0;
            }
            if( isset( $df[$keyword] ) && $df[$keyword] > 0 ) {
                $results[$chunk_id] += $occurences / $df[$keyword];
            }
        }
    }

    arsort( $results );

    return $results;
}