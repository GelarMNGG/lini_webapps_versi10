<?php if (Auth::guard('web')->check()): ?>
    <?php $isVerified = Auth::user()->is_verified; ?>
    <?php if($isVerified == 0): ?>
        <div class="flash-message">
            <div class="alert alert-warning text-center mb-0">
                <?php
                    $dateCreated = Auth::user()->created_at;
                    $date = Carbon\Carbon::parse($dateCreated);
                    $dateRange = $date->addDays(10);
                    $now = Carbon\Carbon::now();

                    //remaining days
                    if ($dateRange > $now) {
                        $diff = $dateRange->diffInDays($now);
                    }else{
                        $diff = 0;
                    }

                    //force logout
                    if ($diff <= 0){
                        echo "
                            Akun Anda telah expired, sistem akan logout dalam beberapa saat. <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                        ";
                        Auth::guard('web')->logout();
                    }else{
                        echo "
                            Akun Anda belum terverifikasi. Dan akan expired dalam <strong> $diff hari</strong> lagi. <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                        ";
                    }
                ?>
            </div>
        </div>
    <?php endif ?>
<?php endif ?>