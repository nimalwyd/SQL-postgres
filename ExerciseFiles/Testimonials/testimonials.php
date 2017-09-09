<?php
/*******************************************************************************
    testimonials.php by Bill Weinman <http://bw.org/contact/>
    for PostgreSQL 9 with PHP Essential Training
    Copyright (c) 2009-2010 The BearHeart Group LLC
*******************************************************************************/

define('TITLE', 'Testimonials');
define('VERSION', '1.0.6');

require_once 'lib/bwPgSQL.php';
define('PGUSER', 'sid');
define('PGPASS', 'foo.bar');
define('DATABASE', 'testimonials');
define('TABLENAME', 'testimonial');

_init();
main();

function main()
{
    global $G;
    global $RECCOUNT;   // import RECCOUNT into this namespace
    isset($RECCOUNT) or $RECCOUNT = 3;   // default to 3

    $db = $G['db'];
    $tname = TABLENAME;
    $idlist = array();

    if(!$db) return;    // this means init failed

    try {
        foreach($db->sql_query("SELECT id FROM $tname") as $rec) {
            $idlist[] = $rec['id'];
        }
    } catch (PDOException $e) {
        return error($e->getMessage());
    }

    // how many ids?
    $idcount = count($idlist);

    // idcount too big?
    $maxcount = floor($idcount / 4);
    if($RECCOUNT > $maxcount) return error(
        "There are $idcount records in the database. " .
        "For good randomness you cannot display more than $maxcount at a time."
    );

    // PHP's array_rand function returns an array of keys
    $result_keys = array_rand($idlist, $RECCOUNT);
    foreach( $result_keys as $k ) {
        printrec($idlist[$k]);
    }
}

function _init( )
{
    global $G;
    try {
        $G['db'] = new bwPgSQL( PGUSER, PGPASS, DATABASE, TABLENAME );
    } catch(PDOException $e) {
        return error($e->getMessage());
    }
}

function printrec( $id )
{
    global $G;
    $db = $G['db'];

    try {
        $rec = $db->get_rec($id);
    } catch (PDOException $e) {
        return error($e->getMessage());
    }

    print("<div class=\"testimonial\">\n");
    printf("<p class=\"testimonial\">%s</p>\n", $rec['testimonial']);
    printf("<p class=\"byline\">&mdash;%s</p>\n", $rec['byline']);
    print("</div>\n");
}

function error( $s )
{
    global $G;
    echo($s);
    return FALSE;
}

?>
