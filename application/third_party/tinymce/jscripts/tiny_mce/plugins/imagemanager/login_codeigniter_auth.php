<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sample login page</title>
</head>
<body>
    Bạn không có quyền sử dụng chức năng quản trị.<br />Vui lòng đăng nhập với tài khoản quản trị.<br />
    <a id="login" href="" target="_top">Đăng nhập</a>
    <script type="text/javascript" language="javascript">
        document.getElementById("login").href = window.top.location.href;
        //window.top.location.href = window.top.location.href;
    </script>
</body>
</html>
