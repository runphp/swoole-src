--TEST--
swoole_server: sendfile
--SKIPIF--
<?php
require __DIR__ . '/../include/skipif.inc';
skip_if_in_valgrind();
?>
--FILE--
<?php
require __DIR__ . '/../include/bootstrap.php';

/**

 * Time: 下午4:34
 */

$simple_tcp_server = __DIR__ . "/../include/api/swoole_server/opcode_server.php";
$port = get_one_free_port();

start_server($simple_tcp_server, TCP_SERVER_HOST, $port);

suicide(2000);
usleep(500 * 1000);

makeTcpClient(TCP_SERVER_HOST, $port, function(\swoole_client $cli) {
    $r = $cli->send(opcode_encode("sendfile", [2, __FILE__]));
    Assert::assert($r !== false);
}, function(\swoole_client $cli, $recv) {
    $len = unpack("N", substr($recv, 0, 4))[1];
    Assert::eq($len - 4, strlen(substr($recv, 4)));
    swoole_event_exit();
    echo "SUCCESS";
});

?>
--EXPECT--
SUCCESS
