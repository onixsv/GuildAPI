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

namespace alvin0319\GuildAPI\form\hasGuild\manage\invitation;

use alvin0319\GuildAPI\Guild;
use alvin0319\GuildAPI\GuildAPI;
use pocketmine\form\Form;
use pocketmine\player\Player;
use function is_bool;

class InvitationInfoForm implements Form{

	/** @var Guild */
	protected Guild $guild;

	protected string $player;

	public function __construct(Guild $guild, string $player){
		$this->guild = $guild;
		$this->player = $player;
	}

	public function jsonSerialize() : array{
		return [
			"type" => "modal",
			"title" => "§l{$this->player}님 가입신청 받기",
			"content" => "정말 {$this->player}님의 가입신청을 수락하시겠습니까?",
			"button1" => "네",
			"button2" => "아니요"
		];
	}

	public function handleResponse(Player $player, $data) : void{
		if(is_bool($data)){
			if($data){
				$join = GuildAPI::getInstance()->addMember($this->player, $this->guild, Guild::MEMBER, true);
				switch($join){
					case GuildAPI::CANT_JOIN_ALREADY_MEMBER:
						$message = "해당 플레이어는 이미 해당 길드의 멤버이거나 다른 길드에 소속되어 있습니다.";
						break;
					case GuildAPI::CANT_JOIN_DUE_TO_FULL:
						$message = "길드가 가득 찼습니다.";
						break;
					case GuildAPI::JOIN_SUCCESS:
					default:
						$message = "";
				}
				if($message !== ""){
					GuildAPI::message($player, $message);
				}else{
					GuildAPI::message($player, "가입 신청을 수락했습니다.");
					$this->guild->getGuildStorage()->removeInvitation($this->player);
				}
			}
		}
	}
}
