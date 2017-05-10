<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <link rel="stylesheet" href="/css/common.css" />
    <link rel="stylesheet" href="/extensions/bootstrap/css/bootstrap.css" />
    <script type="text/javascript" src="/extensions/jquery/external/jquery/jquery.js"></script>
	<title><?php echo App::getParam('title') ?></title>
</head>
    <body>
        <div class="wrapper">
            <header class="page-header">
                <div class="logo">
                    <h1>Taskman BeeJee</h1>
                </div>
                <?php (new authWidget())->init() ?>
            </header>
            <div class="content">
                <?php $this->generateContent() ?>
            </div>
            <footer>
                <p>
                    Powered by Victor Fedorenko
                </p>
            </footer>
        </div>
    </body>
</html>