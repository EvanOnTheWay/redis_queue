<?php

//连接redis数据库
$redis = new Redis(); 
$redis->connect('127.0.0.1', 6379); //连接Redis
$redis->auth('root'); //密码验证
$redis->select(1);//选择数据库2
$redis_name = 'queue';

//模拟100人请求秒杀(高压力)
for ($i = 0; $i < 100; $i++) {
    $uid = rand(10000000, 99999999);
    //获取当前队列已经拥有的数量,如果人数少于十,则加入这个队列
    $num = 10;
    if ($redis->lLen($redis_name) < $num) {
        $redis->rPush($redis_name, $uid);
        echo $uid . "秒杀成功"."<br>";
    } else {
        //如果当前队列人数已经达到10人,则返回秒杀已完成
        echo "秒杀已结束<br>";
    }
}

function build_order_no($uid){
    return  substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).$uid;
}


//设置redis数据库连接及键名

//PDO连接mysql数据库
$dsn = "mysql:dbname=test;host=127.0.0.1";
$pdo = new PDO($dsn, 'root', 'root');
 
//死循环
//从队列最前头取出一个值,判断这个值是否存在,取出时间和uid,保存到数据库
//数据库插入失败时,要有回滚机制
//注: rpush 和lpop是一对
 
while(1) {
    //从队列最前头取出一个值
    $uid = $redis->lPop($redis_name);
    //判断值是否存在
    if(!$uid || $uid == 'nil'){
        echo '存储完毕';break;
    }
    sleep(2);
    //生成订单号
    $orderNum = build_order_no($uid);
    //生成订单时间
    $timeStamp = time();
    //构造插入数组
    $user_data = array('uid'=>$uid,'order_id'=>$orderNum);
    //将数据保存到数据库
    $sql = "insert into panic_buy (uid,order_id) values (:uid,:order_id)";
    $stmt = $pdo->prepare($sql);
    $res = $stmt->execute($user_data);
    //数据库插入数据失败,回滚
    if(!$res){
        $redis->rPush($key,$uid);
    }

}
