<?php
class PdoDb
{
    /**
     * 数据库默认的连接信息
     * @var array
     */
    private $dbConfig = [
        'db' => 'mysql',
        'host' => '192.168.0.7',
        'port' => '3306',
        'user' => 'root',
        'pwd' => '123123',
        'dbname' => 'ceshi',
        'charset' => 'utf8',
    ];
    private $db; //
    private static $instance; //单例模式,本类对象的引用
    /**
     * 私有化构造函数
     * PdoDb constructor.
     * @param $config
     */
    private function __construct($config)
    {
        $this->dbConfig = array_merge($this->dbConfig, $config);
        $this->connect();//连接数据库
    }
    /**
     * 获得单例对象
     * @param array $config 数据库连接信息
     * @return PdoDb
     */
    public static function db($config = [])
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }
    /**
     * pdo 连接数据库
     */
    private function connect()
    {
        $dsn = "{$this->dbConfig['db']}:host={$this->dbConfig['host']};port={$this->dbConfig['port']};dbname={$this->dbConfig['dbname']}";
        $db_options = [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $this->dbConfig['charset']
        ];
        try {
//连接数据库,选择数据库,设置字符集
            $this->db = new PDO($dsn, $this->dbConfig['user'], $this->dbConfig['pwd'], $db_options);
        } catch (PDOException $e) {
//输出异常信息
            echo $e->getMessage() . "
";
            exit('数据库连接失败!');
        }
    }
    /**
     * 增删改
     * @param $sql
     * @return mixed
     */
    public function exec($sql)
    {
        $res = $this->db->exec($sql);
        return $res;
    }
    /**
     * @param $sql
     * @return mixed
     */
    public function query($sql)
    {
        $res = $this->db->query($sql);
        return $res;
    }
    /**
     * 返回所有数据,二维数组
     * @param $sql
     * @return mixed
     */
    public function getAll($sql)
    {
        $res = $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }
    /**
     * 返回一列数据 一维数组
     * @param $sql
     * @param int $num
     * @return mixed
     */
    public function getColumn($sql, $num = 0)
    {
        $res = $this->query($sql);
        $result = [];
        while (($row = $res->fetchColumn($num)) !== false) {
            $result[] = $row;
        }
        return $result;
    }
    /**
     * 返回一行数据 一维数组
     * @param $sql
     * @return mixed
     */
    public function getRow($sql)
    {
        $res = $this->query($sql)->fetch(PDO::FETCH_ASSOC);
        return $res;
    }
    /**
     * 返回一个数据 聚合函数
     * @param $sql
     * @return mixed
     */
    public function getOne($sql)
    {
        $res = $this->query($sql)->fetch(PDO::FETCH_NUM);
        return $res[0];
    }
}