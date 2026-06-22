<?php

interface toba_captcha_interface
{
    public function check(string $response): bool;
    public function get_script(array $params): string;
    public function get_widget(): string;
}
