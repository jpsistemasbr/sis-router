<?php

namespace SisRoute\Http\Body;

use SisRoute\Http\Globals\Server;

interface Handler
{
	public function handle(Server $server);
}