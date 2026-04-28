<?php

/*
        MySQL modul

        Must be defined:

                define ('SQL_HOST', 'localhost');
                define ('SQL_DATABASE', 'db_name');
                define ('SQL_USER', 'root');
                define ('SQL_PASSWORD', '');
*/

global $SQL;
$SQL = new MySQL();

class MySQL
{
        public $mysqli;

    public static function singleton()
    {
                $result = false;

        if ( !isset( self::$mysqli ) ) {

            $result = new mysqli( SQL_HOST, SQL_USER, SQL_PASSWORD );

                        if ( mysqli_connect_errno() )
                        {
                                echo( "Failed to connect, the error message is : ".mysqli_connect_error() );
                                exit();
                        }
        }

                return $result;
    }

        function __construct()
        {
                $this->mysqli = $this->singleton();

                $this->mysqli->select_db( "csft" );

                $this->mysqli->query( "set names utf8" );
        }

        function check_error( $query )
        {
                global $SQL;

                printf( date('d.m.Y H:i:s')." Error: %s<br> <b>%s</b><br>\n", $SQL->mysqli->error, $query );
        }

        function query( $query )
        {
                global $SQL;

                $result = $SQL->mysqli->query( $query );

                if ( !$result ) {

                        $this->check_error( $query );
                }

                return $result;
        }

        function get($query)
        {
                global $SQL;

                $data = $SQL->query($query);

                if (!$data)
                        return false;

                $result = $data->fetch_object();
                $data->free_result();

                return $result;
        }

        function table($query)
        {
                global $SQL;

                $data = $SQL->query($query);

                if (!$data)
                        return false;

                $result = array();

                while ($object = $data->fetch_object())
                        $result[] = $object;

                $data->free_result();

                return $result;
        }

        function value( $query )
        {
                global $SQL;

                $data = $SQL->query($query);

                if (!$data)
                        return false;

                $object = $data->fetch_array();

                $result = $object[0];

                $data->free_result();

                return $result;
        }

        function insert_id()
        {
                global $SQL;

                return $SQL->mysqli->insert_id;
        }

        function affected_rows()
        {
                global $SQL;

                return $SQL->mysqli->affected_rows;
        }

};

?>

