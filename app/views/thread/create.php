<?php $title = "Create a Thread" ?>
<h1>Create a thread</h1>
<?php // Checks for validation errors in the inputs passed
if ($thread->hasError() || $comment->hasError()): ?>
    <div class="alert alert-block">
        <h4 class="alert-heading">Validation error!</h4>
        
        <?php if (!empty($thread->validation_errors['title']['length'])): ?>
        <div>
            <em>Title</em> must be between
            <?php eh($thread->validation['title']['length'][1]) ?> and
            <?php eh($thread->validation['title']['length'][2]) ?> characters in length.
        </div>
        <?php endif ?>
        
        <?php if (!empty($comment->validation_errors['body']['length'])): ?>
        <div>
            <em>Comment</em> must be between
            <?php eh($comment->validation['body']['length'][1]) ?> and
            <?php eh($comment->validation['body']['length'][2]) ?> characters in length.
        </div>
        <?php endif ?>
    </div>
<?php endif ?>

<form class="well" method="post" action="<?php eh(url('')) ?>">
    <label>Title</label>
    <input style='width=100%;' type="text" class="span2" name="title" value="<?php eh(Param::get('title')) ?>">

    <label>Comment</label>
    <textarea style='width=100%;' name="body"><?php eh(Param::get('body')) ?></textarea><br/>
    
    <button type="submit" class="btn btn-primary">Submit</button><br/><br/>
    <a href="<?php eh(url('thread/index'))?>">
        &larr; Back to All Threads
    </a>
</form>
