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
use function array_keys;
use function is_int;

class MemberForceQuitSelectForm implements Form{

	/** @var Guild */
	protected Guild $guild;

	protected array $members = [];

	public function __construct(Guild $guild){
		$this->guild = $guild;
	}

	public function jsonSerialize() : array{
		$this->members = array_keys($this->guild->getMembers());
		$arr = [];
		foreach($this->members as $member){
			$arr[] = ["text" => "{$member}님\n{$this->guild->getRole($member)}"];
		}
		return [
			"type" => "form",
			"title" => "§l멤버 강퇴하기",
			"content" => "강퇴할 멤버를 선택해주세요.",
			"buttons" => $arr
		];
	}

	public function handleResponse(Player $player, $data) : void{
		if(is_int($data)){
			if(isset($this->members[$data])){
				if($this->guild->getLeader() !== GuildAPI::convert($player)){
					if($this->members[$data] === $this->guild->getLeader()){
						GuildAPI::message($player, "길드장을 강퇴할 수 없습니다.");
					}
				}elseif($this->guild->getRole($this->members[$data]) === Guild::CO_LEADER){
					if($this->guild->getRole($player) === Guild::CO_LEADER){
						GuildAPI::message($player, "길드 공동 리더는 강퇴할 수 없습니다.");
					}
				}elseif($this->members[$data] === GuildAPI::convert($player)){
					GuildAPI::message($player, "자기 자신을 강퇴할 수 없습니다.");
				}else{
					$player->sendForm(new MemberForceQuitConfirmForm($this->members[$data], $this->guild));
				}
			}
		}
	}
}
