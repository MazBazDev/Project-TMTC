<?php ?>

<form action="/register" method="post">
    <div class="control-group">
        <!-- Username -->
        <label class="control-label"  for="username">Username</label>
        <div class="controls">
            <input type="text" id="username" name="username" placeholder="" class="input-xlarge">
            <p class="help-block">Username can contain any letters or numbers, without spaces</p>
        </div>
    </div>

    <div class="control-group">
        <!-- E-mail -->
        <label class="control-label" for="email">E-mail</label>
        <div class="controls">
            <input type="text" id="email" name="email" placeholder="" class="input-xlarge">
            <p class="help-block">Please provide your E-mail</p>
        </div>
    </div>

    <div class="control-group">
        <!-- Password-->
        <label class="control-label" for="password">Password</label>
        <div class="controls">
            <input type="password" id="password" name="password" placeholder="" class="input-xlarge">
            <p class="help-block">Password should be at least 4 characters</p>
        </div>
    </div>
    <button type="submit">submit</button>
</form>
