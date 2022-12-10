<?php

set_error_handler([new \App\Exception\ExceptionHandler(), "convertWarningAndNoticesToException"]);
set_exception_handler([new \App\Exception\ExceptionHandler(), "handle"]);


