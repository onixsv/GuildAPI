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

namespace alvin0319\GuildAPI\form\all;

use alvin0319\GuildAPI\Guild;
use alvin0319\GuildAPI\GuildAPI;
use pocketmine\form\Form;
use pocketmine\player\Player;
use function count;
use function is_int;

class GuildInfoForm implements Form{

	/** @var Player */
	protected Player $player;

	/** @var Guild */
	protected Guild $guild;

	public function __construct(Player $player, Guild $guild){
		$this->player = $player;
		$this->guild = $guild;
	}

	public function jsonSerialize() : array{
		$memberCount = count($this->guild->getMembers());
		$onlineMemberCount = count($this->guild->getOnlineMembers());
		$buttons = [["text" => "나가기"]];
		if(!$this->guild->isMember($this->player) && !GuildAPI::getInstance()->hasGuild($this->player)){
			$buttons[] = ["text" => "길드 가입하기"];
		}
		return [
			"type" => "form",
			"title" => "{$this->guild->getName()} 길드 정보",
			"content" => "§l{$this->guild->getName()} 길드의 정보입니다.\n\n길드 멤버 수: {$memberCount}\n길드 온라인 멤버 수: {$onlineMemberCount}\n길드 포인트: {$this->guild->getGuildStorage()->getGuildPoints()}\n길드 리더: {$this->guild->getLeader()}",
			"buttons" => $buttons
		];
	}

	public function handleResponse(Player $player, $data) : void{
		if(is_int($data)){
			switch($data){
				case 1:
					if($this->guild instanceof Guild){
						if($this->guild->getGuildJoinType() === Guild::GUILD_TYPE_OPEN){
							$join = GuildAPI::getInstance()->addMember($player, $this->guild);
							switch($join){
								case GuildAPI::CANT_JOIN_ALREADY_MEMBER:
									$message = "당신은 이미 해당 길드의 멤버이거나 다른 길드에 소속되어 있습니다.";
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
								GuildAPI::message($player, "길드에 가입했습니다.");
							}
						}elseif($this->guild->getGuildJoinType() === Guild::GUILD_TYPE_INVITATION){
							GuildAPI::message($player, "해당 길드는 초대로만 가입할 수 있습니다.");
						}elseif($this->guild->getGuildJoinType() === Guild::GUILD_TYPE_REQUEST_JOIN){
							if(!$this->guild->getGuildStorage()->hasInvitation($player)){
								$this->guild->addInvitation($player);
								GuildAPI::message($player, "길드 가입신청을 넣었습니다.");
							}else{
								GuildAPI::message($player, "이미 해당 길드에 가입신청을 넣었습니다.");
							}
						}
					}
			}
		}
	}
}
