<?php
require("../../class/connect.php");
if(!defined('InEmpireCMS'))
{
	exit();
}
eCheckCloseMods('member');//关闭模块
$myuserid=(int)getcvar('mluserid');
$r=array();
$mhavelogin=0;
if($myuserid)
{
	include("../../class/db_sql.php");
	include("../../member/class/user.php");
	include("../../data/dbcache/MemberLevel.php");
	$link=db_connect();
	$empire=new mysqlquery();
	$mhavelogin=1;
	//数据
	$myusername=RepPostVar(getcvar('mlusername'));
	$myrnd=RepPostVar(getcvar('mlrnd'));
	$qcklgr=qCheckLoginAuthstr();
	if(!$qcklgr['islogin'])
	{
		EmptyEcmsCookie();
		$mhavelogin=0;
	}
	else
	{
		$r=$empire->fetch1("select ".eReturnSelectMemberF('userid,username,groupid,userfen,money,userdate,havemsg,checked')." from ".eReturnMemberTable()." where ".egetmf('userid')."='$myuserid' and ".egetmf('rnd')."='$myrnd' limit 1");
		if(empty($r[userid])||$r[checked]==0)
		{
			EmptyEcmsCookie();
			$mhavelogin=0;
		}
	}
	//会员等级
	if(empty($r[groupid]))
	{$groupid=eReturnMemberDefGroupid();}
	else
	{$groupid=$r[groupid];}
	$groupname=$level_r[$groupid]['groupname'];
	//点数
	$userfen=$r[userfen];
	//余额
	$money=$r[money];
	//天数
	$userdate=0;
	if($r[userdate])
	{
		$userdate=$r[userdate]-time();
		if($userdate<=0)
		{$userdate=0;}
		else
		{$userdate=round($userdate/(24*3600));}
	}
	//是否有短消息
	$havemsg="";
	if($r[havemsg])
	{
		$havemsg="<a href='".$public_r['newsurl']."e/member/msg/' target=_blank><font color=red>您有新消息</font></a>";
	}
	//$myusername=$r[username];
}
if($mhavelogin==1)
{
?>
document.write("&raquo;&nbsp;<font color=red><b><?=$myusername?></b></font>&nbsp;&nbsp;<a href=\"/ecms75/e/member/my/\" target=\"_parent\"><?=$groupname?></a>&nbsp;<?=$havemsg?>&nbsp;<a href=\"/ecms75/e/space/?userid=<?=$myuserid?>\" target=_blank>我的空间</a>&nbsp;&nbsp;<a href=\"/ecms75/e/member/msg/\" target=_blank>短信息</a>&nbsp;&nbsp;<a href=\"/ecms75/e/member/fava/\" target=_blank>收藏夹</a>&nbsp;&nbsp;<a href=\"/ecms75/e/member/cp/\" target=\"_parent\">控制面板</a>&nbsp;&nbsp;<a href=\"/ecms75/e/member/doaction.php?enews=exit&ecmsfrom=9\" onclick=\"return confirm(\'确认要退出?\');\">退出</a>");
<?php
	db_close();
	$empire=null;
}
else
{
?>
document.write("<form name=login method=post action=\"/ecms75/e/member/doaction.php\">    <input type=hidden name=enews value=login>    <input type=hidden name=ecmsfrom value=9>    用户名：<input name=\"username\" type=\"text\" class=\"inputText\" size=\"16\" />&nbsp;    密码：<input name=\"password\" type=\"password\" class=\"inputText\" size=\"16\" />&nbsp;    <input type=\"submit\" name=\"Submit\" value=\"登陆\" class=\"inputSub\" />&nbsp;    <input type=\"button\" name=\"Submit2\" value=\"注册\" class=\"inputSub\" onclick=\"window.open(\'/ecms75/e/member/register/\');\" /></form>");
<?php
}
?>