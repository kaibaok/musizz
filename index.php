<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>Document</title>
</head>
<body>
    <?php 

    require("vendor/autoload.php");

    use GuzzleHttp\Client;

    $listSong = null;

    if(isset($_POST['song'])) {
        $zing = new Zing();
        $msg = $_POST['song'];
        $listSong = $zing->searchSong($msg);
    }
    ?>

    <form action="" method="POST">
        <input type="text" name="song" value="<?= (isset($_POST['song'])) ? $_POST['song'] : '' ?>" placeholder="Tìm tên bài hát ....">
        <button type="submit">Tìm</button>
    </form>

    <br>
    <?php if(!empty($listSong)) { ?>
    <ul>
        <?php foreach ($listSong as $key => $value) {
        } ?>
            <li>
                <b><?= $value['title']?></b> - <i><?= $value['singer']?></i> - <a href="<?= $value['link'] ?>">Link</a>
            </li>
        <?php } ?>
    </ul>
    <?php } ?>
</body>
</html>
