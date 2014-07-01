<?php
/**
 * Dynamic SQL constructor for Datatable Ajax processing
 * @author Hernan Thiers
 * 20140629
 */
class AjaxModelModel{

    /**
    * Create the data output array for the DataTables rows
    *
    *  @param  array $columns Column information array
    *  @param  array $data    Data from the SQL get
    *  @return array          Formatted data in a row based format
    */
    static function dataOutput( $columns, $data )
    {
        $out = array();

        for ( $i=0, $ien=count($data) ; $i<$ien ; $i++ ) {
            $row = array();

            for ( $j=0, $jen=count($columns) ; $j<$jen ; $j++ ) {
                    $column = $columns[$j];

                    // Is there a formatter?
                    if ( isset( $column['formatter'] ) ) {
                            $row[ $column['dt'] ] = $column['formatter']( $data[$i][ $column['db'] ], $data[$i] );
                    }
                    else {
                            $row[$column['dt']] = $data[$i][$columns[$j]['db']];
                    }
            }

            $out[] = $row;
        }

        return $out;
    }
    
    static function dataOutputX($totalColumns, $data){
        $k = 1;
        $output = array();
        
        while($aRow = $data->fetch(PDO::FETCH_NUM))
        {
            $row = array();

            for($i=0;$i<$totalColumns;$i++)
            {
                // FORCE UTF8
                #$row[] = utf8_encode($aRow[ $i ]);
                $row[] = $aRow[$i];
            }

            $output[] = $row;

            $k++;
        }
        
        return $output;
    }


    /**
    * Paging
    *
    * Construct the LIMIT clause for server-side processing SQL query
    *
    *  @param  array $request Data sent to server by DataTables
    *  @param  array $columns Column information array
    *  @return string SQL limit clause
    */
    static function limit ( $request, $columns )
    {
        $limit = '';

        if ( isset($request['start']) && $request['length'] != -1 ) {
                $limit = "LIMIT ".intval($request['start']).", ".intval($request['length']);
        }

        return $limit;
    }


    /**
    * Ordering
    *
    * Construct the ORDER BY clause for server-side processing SQL query
    *
    *  @param  array $request Data sent to server by DataTables
    *  @param  array $columns Column information array
    *  @return string SQL order by clause
    */
    static function order ( $request, $columns )
    {
        $order = '';

        if ( isset($request['order']) && count($request['order']) ) {
            $orderBy = array();
            $dtColumns = AjaxModelModel::pluck( $columns, 'dt' );

            for ( $i=0, $ien=count($request['order']) ; $i<$ien ; $i++ ) {
                    // Convert the column index into the column data property
                    $columnIdx = intval($request['order'][$i]['column']);
                    $requestColumn = $request['columns'][$columnIdx];

                    $columnIdx = array_search( $requestColumn['data'], $dtColumns );
                    $column = $columns[ $columnIdx ];

                    if ( $requestColumn['orderable'] == 'true' ) {
                            $dir = $request['order'][$i]['dir'] === 'asc' ?
                                    'ASC' :
                                    'DESC';

                            $orderBy[] = ''.$column['db'].' '.$dir;
                    }
            }

            $order = 'ORDER BY '.implode(', ', $orderBy);
        }

        return $order;
    }


    /**
    * Searching / Filtering
    *
    * Construct the WHERE clause for server-side processing SQL query.
    *
    * NOTE this does not match the built-in DataTables filtering which does it
    * word by word on any field. It's possible to do here performance on large
    * databases would be very poor
    *
    *  @param  array $request Data sent to server by DataTables
    *  @param  array $columns Column information array
    *  @param  array $bindings Array of values for PDO bindings, used in the
    *    sql_exec() function
    *  @return string SQL where clause
    */
    static function filter ( $request, $columns, &$bindings )
    {
        $globalSearch = array();
        $columnSearch = array();
        $dtColumns = AjaxModelModel::pluck( $columns, 'dt' );

        if ( isset($request['search']) && $request['search']['value'] != '' ) {
                $str = $request['search']['value'];

                for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
                        $requestColumn = $request['columns'][$i];
                        $columnIdx = array_search( $requestColumn['data'], $dtColumns );
                        $column = $columns[ $columnIdx ];

                        if ( $requestColumn['searchable'] == 'true' ) {
                                $binding = AjaxModelModel::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
                                $globalSearch[] = "`".$column['db']."` LIKE ".$binding;
                        }
                }
        }

        // Individual column filtering
        for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search( $requestColumn['data'], $dtColumns );
                $column = $columns[ $columnIdx ];

                $str = $requestColumn['search']['value'];

                if ( $requestColumn['searchable'] == 'true' &&
                 $str != '' ) {
                        $binding = AjaxModelModel::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
                        $columnSearch[] = "`".$column['db']."` LIKE ".$binding;
                }
        }

        // Combine the filters into a single string
        $where = '';

        if ( count( $globalSearch ) ) {
                $where = '('.implode(' OR ', $globalSearch).')';
        }

        if ( count( $columnSearch ) ) {
                $where = $where === '' ?
                        implode(' AND ', $columnSearch) :
                        $where .' AND '. implode(' AND ', $columnSearch);
        }

        if ( $where !== '' ) {
                $where = 'WHERE '.$where;
        }

        return $where;
    }

    /**
    * Crear
    * @param type $joinsString
    * @return string
    */
    static function joins ($joinsString)
    {
       $joins = "";

       $joins = $joinsString;

       return $joins;
    }
    
    /**
     * Pull a particular property from each assoc. array in a numeric array, 
     * returning and array of the property values from each item.
     *
     *  @param  array  $a    Array to get data from
     *  @param  string $prop Property to read
     *  @return array        Array of property values
     */
    static function pluck ( $a, $prop )
    {
            $out = array();

            for ( $i=0, $len=count($a) ; $i<$len ; $i++ ) {
                    $out[] = $a[$i][$prop];
            }

            return $out;
    }
    
    /**
     * Create a PDO binding key which can be used for escaping variables safely
     * when executing a query with sql_exec()
     *
     * @param  array &$a    Array of bindings
     * @param  *      $val  Value to bind
     * @param  int    $type PDO field type
     * @return string       Bound key to be used in the SQL where this parameter
     *   would be used.
     */
    static function bind ( &$a, $val, $type )
    {
            $key = ':binding_'.count( $a );

            $a[] = array(
                    'key' => $key,
                    'val' => $val,
                    'type' => $type
            );

            return $key;
    }
    
    /**
     * Perform the SQL queries needed for an server-side processing requested,
     * utilising the helper functions of this class, limit(), order() and
     * filter() among others. The returned array is ready to be encoded as JSON
     * in response to an AjaxModelModel request, or can be modified if needed before
     * sending back to the client.
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $sql_details SQL connection details - see sql_connect()
     *  @param  string $table SQL table to query
     *  @param  string $primaryKey Primary key of the table
     *  @param  array $columns Column information array
     *  @return array          Server-side processing response array
     */
    public function buildQueryString($request, $table, $columns, $joins)
    {
        #$bindings = array();
        #$db = AjaxModelModel::sql_connect( $sql_details );

        // Build the SQL query string from the request
        $limit = AjaxModelModel::limit( $request, $columns );
        $order = AjaxModelModel::order( $request, $columns );
        $where = AjaxModelModel::filter( $request, $columns, $bindings );
        $joins = AjaxModelModel::joins($joins);

        // Join parts
        $sql_string = "SELECT SQL_CALC_FOUND_ROWS ".implode(", ", AjaxModelModel::pluck($columns, 'db'))."
            FROM $table a
            $joins
            $where
            $order
            $limit";
        
        $dummy = "vengo de ajax!";

        return $sql_string;
    }
    
    public function test($request){
        return $request;
    }
}
?>