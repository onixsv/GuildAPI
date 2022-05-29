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

namespace alvin0319\GuildAPI\form\hasGuild\manage\member;

use alvin0319\GuildAPI\Guild;
use pocketmine\form\Form;
use pocketmine\player\Player;
use function array_keys;
use function is_int;

class GuildCoLeaderManageForm implements Form{

	/** @var Guild */
	protected Guild $guild;

	protected array $members = [];

	public function __construct(Guild $guild){
		$this->guild = $guild;
	}

	public function jsonSerialize() : array{
		$members = [];
		foreach(array_keys($this->guild->getMembers()) as $member){
			if($member !== $this->guild->getLeader()){
				$members[] = $member;
			}
		}
		$this->members = $members;
		$arr = [];
		foreach($members as $member){
			$arr[] = ["text" => "§l{$member}님\n{$this->guild->getRole($member)}"];
		}
		return [
			"type" => "form",
			"title" => "§l공동 리더 관리",
			"content" => "관리할 멤버를 선택해주세요.",
			"buttons" => $arr
		];
	}

	public function handleResponse(Player $player, $data) : void{
		if(is_int($data)){
			if(isset($this->members[$data])){
				$player->sendForm(new CoLeaderSetForm($this->members[$data], $this->guild));
			}
		}
	}
}
