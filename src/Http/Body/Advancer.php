<?php

namespace SisRoute\Http\Body;

interface Advancer
{
	public function next(Handler $handler);
}