<?php

// 应用公共文件


function C($name){
    return \think\Config::get(strtolower($name));
}


//实例化Logic
function O($name){
    $obj =  \think\Loader::Model(ucfirst(strtolower($name)) , "logic");
    if (method_exists($obj  , "_initialize")) {
        $obj->_initialize();
    }
    return $obj;
}

//实例化model
function D($name){
    return \think\Loader::Model(ucfirst(strtolower($name)) , "model");
}

//实例化mongo
function M($name){
    return \think\Loader::Model(ucfirst(strtolower($name)) , "mongo");
}


/**
 * p 输出各种类型的数据，调试程序时打印数据使用。  参数：可以是一个或多个任意变量或值
 * 
 * @Access public
 * @Return void
 */
function p() 
{
	$args = func_get_args (); //获取多个参数
    echo '<div style="width:100%;text-align:left"><pre style="background:#F6FAF2;">' . "\n";
	//多个参数循环输出
	foreach ( $args as $arg ) 
    {
        if (is_array ( $arg ) || is_object ( $arg )) {
            print_r ( $arg );
            echo '<br>' . "\n";
        } else {
            var_dump ( $arg );
            echo '<br>' . "\n";
        }
	}
    echo '</pre></div>' . "\n";
}


/**
 * throw_error 错误输出
 * 
 * @Param $msg 
 * @Access public
 * @Return void
 */
function throw_error($msg = '')
{
    $trace = tracedebug();
    $show = false;
    $show  = true;
    if($show)
    {
		echo "<pre>";
		print_r($msg);
		print_r($trace);
		echo "</pre>";
    }

    vlog($msg . ':' . json_encode($trace));
	return false;
}


/**
 * tracedebug 打印脚本的trace路径
 * 
 * @Access public
 * @Return void
 */
function tracedebug()
{
	$result = debug_backtrace();
	$data = array();
	foreach ($result as $val)
	{
        if(!isset($val['file']))
        {
            $class = isset($val['class']) ? $val['class'] : '';
            $function = isset($val['function']) ? $val['function'] : '';
		    $data[] = '___class___:'.$class . '[___function___: '.$function.']';
        }else{
		    $data[] = $val['file'] . '[line:'.$val['line'].']';
        }
	}
	return $data;
}

function debugtrace(){
    return tracedebug();
}


/**
 * vlog 代码记录日志
 * 
 * @Access public
 * @Return void
 */
function vlog() {

    $args = func_get_args();
    foreach ($args as &$arg)
    {
        if (is_array($arg))
            $arg = json_encode($arg);
    }
    $args = implode("\t", $args);

    $username = "";
    $backtrace = debug_backtrace();
    $method = "";
    if (count($backtrace) > 1) {
        $method = sprintf("%s\t%s::%s\t%s:%s", strftime("%Y-%m-%d %H:%M:%S"), isset($backtrace[1]['class']) ? $backtrace[1]['class'] : '', isset($backtrace[1]['function']) ? $backtrace[1]['function'] : '' , basename($backtrace[0]['file']), $backtrace[0]['line']); 
    }

    $username = "";
    $content = sprintf("%s\t%s\t%s", $method, $username, $args);
   
    $file = dirname(__DIR__) . "/runtime/log/vlog.".date("Ymd");
    $chmod = 0;
    if(!file_exists($file)) {
        $chmod = 1;
    }
    file_put_contents($file, $content."\n", FILE_APPEND | LOCK_EX);
    if($chmod)  chmod($file, 0777);
}


/**
 * myconst 查看定义的常量
 * 
 * @Param $isArray 
 * @Access public
 * @Return void
 */
function myconst($isArray = FALSE) 
{
	$const = get_defined_constants ( true );
	return $isArray ? p ( $const ['user'] ) : $const ['user'];
}



/**
 * Computation 转化二维数组为一维 , 生成键值对的格式
 * 
 * @Param $data 
 * @Param 	string	 $IDfield 
 * @Param 	string	 $hitsField 
 * @Access public
 * @Return void
 */
function Computation($data , $IDfield = 'value_id' , $hitsField = 'hits')
{
	$return = array();
	if (empty($data)) return array();
	foreach ($data as $val)
	{
		if (!isset($return[$val[$IDfield]]))
		{
			$return[$val[$IDfield]] = $val[$hitsField];
		} else {
			$return[$val[$IDfield]] += $val[$hitsField];	//相同的值会相加, 使用请注意
		}
	}
	unset($data);
	return $return;
}


/**
 * arrayToSimple 二维数组转一维 === arrayToSimple($data , $field);
 * 
 * @Param $items 
 * @Param 	string	 $field 
 * @Access public
 * @Return array()
 */
function arrayToSimple($items, $field = 'id')
{
	$values = array();
	if(!is_array($items) || empty($items)) return array();
	foreach ($items as $item)
	{
		$values[] = $item[$field];
	}
	unset($items);
	return $values;
}




/**
 * data_trun_key 转换数组为指定键映射
 * 
 * @Param $data 数据
 * @Param $field 字段
 * @Param $primary 是否是主键, 如果是唯一的, 也可以用true
 * @Access private
 * @Return void
 */
function data_trun_key($data , $field , $primary = false)
{
    $return = array();
    if(empty($data)) return $return;
    foreach($data as $val)
    {
        $key = $val[$field];
        if(!isset($return[$key])) $return[$key] = array();
        if(!$primary)
        {
            $return[$key][] = $val;
        } else {
            $return[$key] = $val;
        }
    }
    return $return;
}




