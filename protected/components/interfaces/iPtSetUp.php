<?php
interface iPtSetUp
{
	/**
	 * Returns true if setup is complete false if not. All setup models must implement this method
	 */
	public static function getSetUpIsComplete();
}
