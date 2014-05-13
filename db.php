<?php 

/**
* @copyright Copyright (C) 2010 Nadir Boussoukaia. All rights reserved.
*/



class Inducido {
    public static $Path_To_Data=".";                             
    public static $project_name_tech="reservation";                             

}

function die_fatal_error($message)
{

    die($message);
}

function add2statusbar($msg, array $values = NULL,$category= NULL)  //info
{     
    echo empty($values) ? $string : strtr($string, $values);
}

function add2statusbarKO($msg, array $values = NULL,$category= NULL)  //failed
{      
    echo empty($values) ? $string : strtr($string, $values);
}

function add2statusbarOK($msg, array $values = NULL,$category= NULL)  //success
{      
    echo empty($values) ? $string : strtr($string, $values);
}
//----------------------------------------------------------------------
class DB
//----------------------------------------------------------------------
{
    /* connexion à la base de donnée */ 

    private static $db_init_called=false;
    private static $lastSQL="";
    private static $dbHandle="";
    private static $dburl=null;
    private static $typeDb='sqlite';

    private static $instance;


    public static $db_server = '127.0.0.1';
    public $db_port = '5984';



    public static function instance($typeDb='sqlite',$dsn=null) {
        if (!isset(self::$instance)) {
            self::$instance = new DB($typeDb,$dsn);
        }
        return self::$instance;
    }

    // realise la connexion a la base de donnees
    public static function init()
    {
        //lazy connect (on first sql call)
        if(DB::$db_init_called) return;

        DB::$db_init_called=true;

        //TODO faire mieux
        $ext_names=get_loaded_extensions();

        if (!in_array('PDO',$ext_names))
            die('no PDO extension');

        self::$typeDb='sqlite';
        //$typeDb='mysql';

        //voir pour les URL:  http://php.developpez.com/faq/?page=pdo#pdo-connect

        //[PDO::getAvailableDrivers] array ( 0 => 'mysql', 1 => 'sqlite', 2 => 'sqlite2', )
        switch(self::$typeDb)
        {


            case 'sqlite':
                if (!in_array('sqlite',PDO::getAvailableDrivers()))
                die('<pre>No SQLITE PDO extension<br>'.print_r(PDO::getAvailableDrivers(),true));

                $username="";
                $password="";
                self::$dburl='sqlite:'.Inducido::$Path_To_Data.'/'.strtolower(Inducido::$project_name_tech).'.db3';
                $driver_options= null;
                break;

            

            default:
                die("non géré");
        }


        try{
            //__construct ( string $dsn [, string $username [, string $password [, array $driver_options ]]] )
            DB::$dbHandle = new PDO(self::$dburl,$username,$password,$driver_options);
            //PDO doit gerer les erreurs sous forme d'exception
            DB::$dbHandle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // les noms de champs seront en caractères minuscules
            DB::$dbHandle->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);



        }catch(PDOException $e){
            //ici si PDO ne trouve pas le fichier, il faut afficher le message!!!
            //$msg = 'ERREUR PDO dans ' . $e->getFile() . ' L.' . $e->getLine() . ' : ' . $e->getMessage();

            DB::$dbHandle=null;


                add2statusbarKO("[PDO ERROR: {msg}]",array('{msg}'=>$e->getMessage()),'core' );
                add2statusbarKO("At {pos}",array('{pos}'=>get_caller_position()),'core' );

            die_fatal_error("ERROR on database access");
        }
    }

    private static function _SQLquote($val)
    {
        return "'".str_replace("'" , "''", $val)."'";
    }

    public static function quote($field)
    {
       // if(empty($field)) sinon empeche les valeur 0!
//         if($field==null || trim($field)=="")
//         return $field;
         
        DB::init();
        if(!isset(DB::$dbHandle)) return DB::_SQLquote($field);            
        else return DB::$dbHandle->quote($field)  ;
    }

    //----------------------------------------------------------------------
        public static function query_single_row($sql)
        {
            $result = DB::_internal_query($sql);
            if($result)
            {
                     $rowdata=$result->fetch();  
                     return $rowdata;
            }
            return null;
        }
    //----------------------------------------------------------------------
    /*devrait renvoyer un tableau */
        public static function query_all($sql)
        {
            $result = DB::_internal_query($sql);
            if($result)
            {
                     $rowdata=$result->fetchAll();  
                     return $rowdata;
            }
            return array();
        }

    //----------------------------------------------------------------------
        public static function query_single_field($sql, $defaultvalue=null)
        {
            $result = DB::_internal_query($sql);
            if($result)
            {
                     $field=$result->fetchColumn();  
                     return $field;
            }
            else
            if($defaultvalue)
            return $defaultvalue;
            
            return null;
        }


    //----------------------------------------------------------------------
    /**
    * execute un ordre DML - initialisation connexion si besoin
    * 
    * @param mixed $sql
    * @return PDO:resultset or false or number of affected rowss
    */
    public static function query($sql)
    {
        return self::_internal_query($sql);
    }
    //----------------------------------------------------------------------
    /**
    * execute un ordre DML - initialisation connexion si besoin
    * 
    * @param mixed $sql
    * @return PDO:resultset or false or number of affected rowss
    */
    private static function _internal_query($sql,$depth_add=0)
    {
        if(empty($sql))
        throw new Exception("Error: empty SQL");
        
         
        //lazy connect (on first sql call)
        if(!DB::$db_init_called)
        DB::init(); 
            
        if(DB::$dbHandle==null)
        return null;

        DB::$lastSQL=$sql;
        try {

            if( strtolower(substr(trim($sql),0,strlen("select")))=="select")
            //query() retourne un jeu de résultats sous la forme d'un objet PDOStatement
            {
                $result = DB::$dbHandle->query($sql);
                if($result) $result->setFetchMode(PDO::FETCH_ASSOC);
            }
            else
                //exec() retourne uniquement le nombre de lignes affectées. 
                $result = DB::$dbHandle->exec($sql);

            //todo $result possede une propriété queryString = le SQL
            if($result===FALSE)
                self::showDBError(DB::$dbHandle,$sql);                   

        } catch (PDOException $e) {
         

            
            echo " [SQL ERROR: ".$e->getMessage()."']"." -->".$sql;                    
            
            return false;
        }


        
        return $result;    
    }    


        
    ///----------------------------------------------------------------------
    static function lastInsertId()
    {
         if(DB::$dbHandle!=null)
        {
              $newid=DB::$dbHandle->lastInsertId();
                 return $newid;
        }
        
        return -1;
    }
    ///----------------------------------------------------------------------
    static function rowCount()
    {
         if(DB::$dbHandle!=null)
         return DB::$dbHandle->rowCount();
        
        return 0;
    }
    
   
    ///----------------------------------------------------------------------
    //todo devenue obsolete?? avec le catch?
    function showDBError($dbHandle,$sql="")
    {
        if($dbHandle!=null)
        {
            $err=$dbHandle->errorInfo();
            if($err!=null && count($err)>=2)
            {
                add2statusbarKO("[ERREUR SQLSTATE '".$err[0]."']");
                add2statusbarKO("[ERREUR DB '".$err[1]."']");
                add2statusbarKO("[ERREUR MSG '".$err[2]."']");
                //en mode prod, ne pas afficher le SQL !!!!
                if(!Inducido::prod_environment())
                if($sql!="")
                    add2statusbarKO("[SQL: $sql]");
            }
        }
    }        

    //----------------------------------------------------------------------
    function DBErrorAsString()
    {
        $s="";
        if(DB::$dbHandle!=null)
        {
            $err=DB::$dbHandle->errorInfo();
            if($err!=null && count($err)>=2)
            {
                $s=("[ERREUR SQLSTATE '".$err[0]."']");
                $s.=("[ERREUR DB '".$err[1]."']");
                $s.=("[ERREUR MSG '".$err[2]."']");
                if(DB::$lastSQL!="")
                    $s.=("[SQL: ".DB::$lastSQL."]");
            }
        }
        return $s;
    }        
    //----------------------------------------------------------------------

    

    
     /* Démarre une transaction, désactivation de l'auto-commit */
 static function beginTransaction()
    {
         if(DB::$dbHandle!=null)
        {
              $newid=DB::$dbHandle->beginTransaction();
              return $newid;
        }
        
        return -1;
    }
    static function commit()
    {
         if(DB::$dbHandle!=null)
        {
              $newid=DB::$dbHandle->commit();
              return $newid;
        }
        
        return -1;
    }
    
    static function now()
    { return DB::sysdate(); }
    
    //usage
    //$result=DB::query("UPDATE todo SET date=".DB::sysdate());

    static function sysdate()
    {
        return "datetime('now')";
                                  
    }

    public static function url(){         //lazy connect (on first sql call)
        if(!DB::$db_init_called)
        DB::init();    return self::$dburl; }    
}

