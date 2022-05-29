<?php

/*
 *
 *  /$$$$$$            /$$ /$$       /$$  /$$$$$$  /$$$$$$$  /$$$$$$
 * /$$__  $$          |__/| $$      | $$ /$$__  $$| $$__  $$|_  $$_/
 * | $$  \__/ /$$   /$$ /$$| $$  /$$$$$$$| $$  \ $$| $$  \ $$  | $$
 * | $$ /$$$$| $$  | $$| $$| $$ /$$__  $$| $$$$$$$$| $$$$$$$/  | $$
 * | $$|_  $$| $$  | $$| $$| $$| $$  | $$| $$__  $$| $$____/   | $$
 * | $$  \ $$| $$  | $$| $$| $$| $$  | $$| $$  | $$| $$        | $$
 * |  $$$$$$/|  $$$$$$/| $$| $$|  $$$$$$$| $$  | $$| $$       /$$$$$$
 *  \______/  \______/ |__/|__/ \_______/|__/  |__/|__/      |______/
 *
 * Copyright (C) 2020 alvin0319
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace alvin0319\GuildAPI\store;

use alvin0319\GuildAPI\Guild;
use alvin0319\GuildAPI\GuildAPI;
use BadMethodCallException;
use function date;
use function time;

class GuildStorage{

	/** @var Guild */
	protected Guild $guild;

	protected int $guildPoints = 0;

	protected bool $allowNameTag = false;

	/** @var bool|string */
	protected $announce = false;

	protected int $guildCount = 15;

	protected array $invitations = [];

	public function __construct(int $guildPoints, bool $allowNameTag, $announce, int $guildCount, array $invitations){
		$this->guildPoints = $guildPoints;
		$this->allowNameTag = $allowNameTag;
		$this->announce = $announce;
		$this->guildCount = $guildCount;
		$this->invitations = $invitations;
	}

	public function setGuild(Guild $guild) : void{
		if(!empty($this->guild)){
			throw new BadMethodCallException("Property `guild` is already Guild");
		}
		$this->guild = $guild;
	}

	public function addInvitation($player) : bool{
		$player = GuildAPI::convert($player);
		if(isset($this->invitations[$player])){
			return false;
		}
		$this->invitations[$player] = date("Y년 m월 d일 H시 i분 s초", time());
		return true;
	}

	public function removeInvitation($player) : bool{
		$player = GuildAPI::convert($player);
		if(!isset($this->invitations[$player])){
			return false;
		}
		unset($this->invitations[$player]);
		return true;
	}

	public function getGuild() : Guild{
		return $this->guild;
	}

	public function getGuildPoints() : int{
		return $this->guildPoints;
	}

	public function setGuildPoints(int $point) : void{
		$this->guildPoints = $point;
	}

	/**
	 * @return bool|string
	 */
	public function getAnnounce(){
		return $this->announce;
	}

	public function getGuildInvitations() : array{
		return $this->invitations;
	}

	public function hasInvitation($player) : bool{
		return isset($this->invitations[GuildAPI::convert($player)]);
	}

	public function getAllowNameTag() : bool{
		return $this->allowNameTag;
	}

	public function setAllowNameTag(bool $value) : void{
		$this->allowNameTag = $value;
	}

	public function setAnnounce($value) : void{
		$this->announce = $value;
	}

	public function getGuildCount() : int{
		return $this->guildCount;
	}

	public function setGuildCount(int $guildCount) : void{
		$this->guildCount = $guildCount;
	}

	public function jsonSerialize() : array{
		return [
			"guildPoints" => $this->guildPoints,
			"allowNameTag" => $this->allowNameTag,
			"announce" => $this->announce,
			"guildCount" => $this->guildCount,
			"invitations" => $this->invitations
		];
	}

	public static function jsonDeserialize(array $data) : GuildStorage{
		return new GuildStorage($data["guildPoints"], $data["allowNameTag"], $data["announce"], $data["guildCount"], $data["invitations"]);
	}
}
