<?php if (!defined('THINK_PATH')) exit();?><html>
<head>
    <title>安装向导 - 海洋CMS</title>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <link href="/Public/Install/css/style.css" rel="stylesheet" type="text/css"/>
    <script src="/Public/Install/js/jquery.js" type="text/javascript"></script>
</head>

<body scroll="no">
<div class="b">
    <div class="main">
        <div class="head">
            <div class="h_right"><a href="//www.seacms.net" target="_blank">官方网站</a><span>|</span><a
                    href="//www.seacms.net" target="_blank">交流论坛</a></div>
            <img src="/Public/Install/images/logo.png"/>
        </div>
        <div class="cont">
            <div class="c_top"></div>
            <div class="c_c">
                <div class="c_c_left">
                    <ul>
                        <li class="on listep0">1、欢迎界面</li>
                        <li class="listep1">2、阅读协议</li>
                        <li class="listep2">3、环境检测</li>
                        <li class="">4、参数配置</li>
                        <li class="">5、安装完成</li>
                    </ul>
                </div>
                <div class="c_c_right">

                    <div class="divstep0">
                        <div class="content">
                            <h2>欢迎使用海洋CMS开始您的筑梦之旅</h2>
                            <div style="font-size:77px; color:#146098;text-align:center;font-weight:bold; margin-top:20%;letter-spacing:6px;">
                                SEACMS
                            </div>
                            <div style="font-size:44px; color:#3c89c3;text-align:center;letter-spacing:4px;">简单&nbsp;·&nbsp;快速&nbsp;·&nbsp;稳定&nbsp;·&nbsp;开源</div>
                        </div>
                        <div class="button"><a onclick="step(1)">开始</a></div>
                    </div>

                    <div class="divstep1" style="display: none">
                        <div class="content">
                            <h2>阅读海洋CMS使用协议和版权声明</h2>
                            <p>版权所有 (c) 2019，海洋CMS保留所有权利。
                            <p>感谢您选择海洋CMS，希望我们的努力能为您提供一个简单、强大的站点解决方案。
                            <p>
                                在您使用海洋CMS建站系统前，请仔细阅读以下信息。除本协议中明确赋予用户的权利、其它权利均保留。如果您不同意以下软件使用许可协议，您不应访问本站/下载本软件，请停止访问和使用，并将其从您的电脑中删除。
                            <p>
                                作者开发海洋CMS初衷是作为学习和交流之用，完全免费开源，用户可以非商业性/商业性地下载、安装、复制和散发本软件产品。也可基于本系统进行二次开发、重新演绎发布等，但务必保留本程序版权标示。
                            <p>开发者不提供任何形式的商业服务，包括但不限于：技术支持，二次开发，模板设计，插件开发等。官网社区、QQ群等交流工具也仅限用户进行技术交流，无任何商业行为。
                                经过大量的测试和修复，程序尚未发现重大问题。但限制于个人能力、时间精力等因素，不可避免的会出现各种问题。您出于自愿而使用本程序，您必须了解使用风险，开发者不提供任何形式的使用担保，也不承担任何的相关责任。
                            <p>作为使用者，您拥有使用本程序搭建网站全部内容的所有权，并独立承担这些内容的法律义务。开发者不对使用本系统构建网站的任何信息内容以及导致的任何版权纠纷和法律争议承担责任。
                            <p>用户使用本软件时，不得用于从事违反中国人民共和国相关法律的活动，海洋CMS对于用户擅自使用本软件从事违法活动不承担任何责任，包括但不限于刑事责任、行政责任、民事责任等。
                                当您访问海洋CMS网站/社区论坛时，请严格遵守相关规定。严禁任何人以任何形式发表违反中华人民共和国法律法规的言论。海洋CMS保留删除内容，封禁账号的权利，情节严重者上报国家公安机关。
                            <p>
                                开发者（海洋）拥有海洋CMS的全部知识产权，包括商标和著作权。本软件只供许可协议，并非出售。只允许您在遵守本协议各项条款的情况下复制、下载、安装、使用或者以其他方式受益于本软件的功能或者知识产权。

                            <p>海洋CMS遵循GPL开源协议（主题和插件除外），在遵守GPL协议、保留海洋CMS版权的情况下，您可以免费使用本系统。
                            <p><strong>您一旦选择安装本程序，即被视为完全理解并接受上述声明条款!</strong>
                        </div>
                        <div class="button"><a onclick="step(2)">我同意</a> <a onclick="step(0)">上一步</a></div>

                    </div>

                    <div class="divstep2" style="display: none">
                        <div class="content">
                            <h2>检测服务器运行环境和文件权限</h2>
                            <table class="tb">
                                <tr>
                                    <th width="170"><strong>需开启的配置</strong></th>
                                    <th width="80"><strong>要求</strong></th>
                                    <th width="400"><strong>实际状态及建议</strong></th>
                                </tr>
                                <tr>
                                    <td>PHP 版本</td>
                                    <td>5.x</td>
                                    <td><font color=green>[√]On</font>
                                        <small> (本程序仅支持PHP5.x，不支持PHP7，当前版本为5.6.27)</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td>fsockopen</td>
                                    <td>On</td>
                                    <td><font color=green>[√]On</font>
                                        <small>(不符合要求将导致采集、远程资料本地化等功能无法应用)</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td>iconv</td>
                                    <td>On</td>
                                    <td><font color=green>[√]On</font>
                                        <small>(不符合要求将导致部分编码问题)</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td>allow_url_fopen</td>
                                    <td>On</td>
                                    <td><font color=green>[√]On</font>
                                        <small>(不符合要求将导致采集、远程资料本地化等功能无法应用)</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td>safe_mode</td>
                                    <td>Off</td>
                                    <td><font color=green>[√]Off</font>
                                        <small>(本系统不支持在<span class="STYLE2">非win主机的安全模式</span>下运行)</small>
                                    </td>
                                </tr>

                                <tr>
                                    <td>GD 支持</td>
                                    <td>On</td>
                                    <td><font color=green>[√]On</font>
                                        <small>(不支持将导致与图片相关的大多数功能无法使用或引发警告)</small>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Curl 支持</td>
                                    <td>On</td>
                                    <td><font color=green>[√]On</font>
                                        <small>(不支持将无法进行百度推送和图片同步等操作)</small>
                                    </td>
                                </tr>

                                <tr>
                                    <td>MySQL 支持</td>
                                    <td>On</td>
                                    <td><font color=green>[√]On</font>
                                        <small>(不支持无法使用本系统)</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td>短标签 支持</td>
                                    <td>On</td>
                                    <td>
                                        <font color=green>[√]On</font>
                                        <small>(数据备份使用帝国备份王核心，要求开启short_open_tag)</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td>SSL 支持</td>
                                    <td>On</td>
                                    <td>
                                        <font color=#B8860B>[×]Off</font>
                                        <small>(邮件SMTP发信服务要求开启SSL支持)</small>
                                    </td>
                                </tr>
                            </table>

                            <table class="tb">
                                <tr>
                                    <th width="300"><strong>目录名</strong></th>
                                    <th width="212"><strong>读取权限</strong></th>
                                    <th width="212"><strong>写入权限</strong></th>
                                </tr>
                                <tr>
                                    <td>/</td>
                                    <td><font color=green>[√]读</font></td>
                                    <td><font color=green>[√]写</font></td>
                                </tr>
                                <tr>
                                    <td>/data</td>
                                    <td><font color=green>[√]读</font></td>
                                    <td><font color=green>[√]写</font></td>
                                </tr>
                                <tr>
                                    <td>/data/admin</td>
                                    <td><font color=green>[√]读</font></td>
                                    <td><font color=green>[√]写</font></td>
                                </tr>
                                <tr>
                                    <td>/data/cache</td>
                                    <td><font color=green>[√]读</font></td>
                                    <td><font color=green>[√]写</font></td>
                                </tr>
                                <tr>
                                    <td>/data/mark</td>
                                    <td><font color=green>[√]读</font></td>
                                    <td><font color=green>[√]写</font></td>
                                </tr>
                                <tr>
                                    <td>/install</td>
                                    <td><font color=green>[√]读</font></td>
                                    <td><font color=green>[√]写</font></td>
                                </tr>
                                <tr>
                                    <td>/uploads/allimg</td>
                                    <td><font color=green>[√]读</font></td>
                                    <td><font color=green>[√]写</font></td>
                                </tr>
                                <tr>
                                    <td>/uploads/editor</td>
                                    <td><font color=green>[√]读</font></td>
                                    <td><font color=green>[√]写</font></td>
                                </tr>
                                <tr>
                                    <td>/uploads/litimg</td>
                                    <td><font color=green>[√]读</font></td>
                                    <td><font color=green>[√]写</font></td>
                                </tr>
                                <tr>
                                    <td>/admin</td>
                                    <td><font color=green>[√]读</font></td>
                                    <td><font color=green>[√]写</font></td>
                                </tr>
                                <tr>
                                    <td>/admin/ebak/bdata</td>
                                    <td><font color=green>[√]读</font></td>
                                    <td><font color=green>[√]写</font></td>
                                </tr>
                                <tr>
                                    <td>/admin/ebak/zip</td>
                                    <td><font color=green>[√]读</font></td>
                                    <td><font color=green>[√]写</font></td>
                                </tr>
                                <tr>
                                    <td>/js</td>
                                    <td><font color=green>[√]读</font></td>
                                    <td><font color=green>[√]写</font></td>
                                </tr>
                                <tr>
                                    <td>/js/player</td>
                                    <td><font color=green>[√]读</font></td>
                                    <td><font color=green>[√]写</font></td>
                                </tr>
                                <tr>
                                    <td>/js/ads</td>
                                    <td><font color=green>[√]读</font></td>
                                    <td><font color=green>[√]写</font></td>
                                </tr>
                            </table>


                        </div>
                        <div class="button"><a class="" onclick="step(3)">下一步</a>
                            <a onclick="step(1)">上一步</a></div>

                    </div>

                </div>
            </div>
            <div class="c_btm"></div>
        </div>
    </div>
</div>

<script src="/Public/Install/js/install.js" type="text/javascript"></script>
<div style="display:none;">
    <script>
        var nowstep = 0;

        function step(nextsum) {
            $('.divstep' + nowstep).hide();
            $('.divstep' + nextsum).show();
            $('.listep' + nowstep).removeClass('on');
            $('.listep' + nextsum).addClass('on');
            nextsum > nowstep ? nowstep++ : nowstep--;
            console.log('this page is : ' + nowstep);
        }
    </script>
</div>
</body>
</html>