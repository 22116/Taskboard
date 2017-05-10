<form id="formcreator" method="post" enctype="multipart/form-data">
    <div class="input-group">
        <div>
            <label>Name:</label>
            <input name="name" type="text" class="form-control" placeholder="Username" aria-describedby="basic-addon1">
        </div>
        <div>
            <label>Email:</label>
            <input name="mail" type="email" class="form-control" placeholder="example@com.ua" aria-describedby="basic-addon1">
        </div>
        <textarea name="content" class="form-control" placeholder="Very important task..." aria-describedby="basic-addon1"></textarea>
        <input id="file" name="image" multiple accept="image/png,image/jpeg,image/gif" type="file">
        <div>
            <input class="btn btn-primary btn-md" type="submit" value="Publish">
            <input class="btn btn-primary btn-md" type="button" value="Preview">
        </div>
    </div>
</form>
<div id="preview">
    <div class='row'><div class="col-sm-6 col-md-4">
        <div class="thumbnail">
            <div id='output'></div>
            <div class="caption">
                <h3></h3>
                <h3></h3>
                <p></p>
                <input type='button' class="btn btn-primary" value='Close'>
                </div>
            </div>
        </div>
    </div></div>
<script type="text/javascript" src="/scripts/preview.js"></script>