<DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
        <link href="{{ asset('css/email.css') }}" id="app-stylesheet" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <div class="container">
            <?php
                /* singgle embeded image */ 
                /*
                $pathToImage = 'img/expenses/tech/'.$email_data['image'];
                */
            ?>
            <?php 
                /*
                <img src="{{ $message->embed($pathToImage) }}">
                */ 
            ?>

            @if(isset($email_data['images']))
                @foreach($email_data['images'] as $image)
                    <img src="{{ $message->embed('img/expenses/tech/'.$image->image) }}"><br>
                @endforeach
            @endif
    
            <p>
                Terima kasih, <br>
                
                <span class="dark">{{ ucfirst(Auth::user()->firstname).' '.ucfirst(Auth::user()->lastname) }}</span>
            </p>
    
        </div>
    </body>
</html>