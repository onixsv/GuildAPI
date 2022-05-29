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
use alvin0319\GuildAPI\GuildAPI;
use pocketmine\form\Form;
use pocketmine\player\Player;
use function is_bool;

class CoLeaderSetForm implements Form{

	protected string $member;

	/** @var Guild */
	protected Guild $guild;

	public function __construct(string $member, Guild $guild){
		$this->member = $member;
		$this->guild = $guild;
	}

	public function jsonSerialize() : array{
		if($this->guild->hasPermission($this->member)){
			return [
				"type" => "modal",
				"title" => "§l{$this->member}님 공동리더 해지",
				"content" => "정말 {$this->member}님의 공동리더 권한을 해지하시겠습니까?",
				"button1" => "네",
				"button2" => "아니요"
			];
		}else{
			return [
				"type" => "modal",
				"title" => "§l{$this->member}님 공동리더 추가",
				"content" => "정말 {$this->member}님에게 공동리더 권한을 부여하시겠습니까?",
				"button1" => "네",
				"button2" => "아니요"
			];
		}
	}

	public function handleResponse(Player $player, $data) : void{
		if(is_bool($data)){
			if($this->guild->hasPermission($this->member)){
				$this->guild->setRole($this->member, Guild::MEMBER);
				GuildAPI::message($player, "{$this->member}님의 공동리더 권한을 해지했습니다.");
			}else{
				$this->guild->setRole($this->member, Guild::CO_LEADER);
				GuildAPI::message($player, "{$this->member}님에게 공동리더 권한을 부여했습니다.");
			}
		}
	}
}
