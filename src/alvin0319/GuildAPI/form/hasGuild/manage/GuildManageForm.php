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

namespace alvin0319\GuildAPI\form\hasGuild\manage;

use alvin0319\GuildAPI\form\hasGuild\manage\guild\GuildCloseForm;
use alvin0319\GuildAPI\form\hasGuild\manage\guild\GuildJoinTypeSetForm;
use alvin0319\GuildAPI\form\hasGuild\manage\invitation\InvitationListForm;
use alvin0319\GuildAPI\form\hasGuild\manage\member\GuildCoLeaderManageForm;
use alvin0319\GuildAPI\form\hasGuild\manage\member\MemberForceQuitSelectForm;
use alvin0319\GuildAPI\form\hasGuild\manage\shop\GuildShopSelectForm;
use alvin0319\GuildAPI\Guild;
use alvin0319\GuildAPI\GuildAPI;
use pocketmine\form\Form;
use pocketmine\player\Player;
use function is_int;

class GuildManageForm implements Form{

	protected Guild $guild;

	public function __construct(Guild $guild){
		$this->guild = $guild;
	}

	public function jsonSerialize() : array{
		return [
			"type" => "form",
			"title" => "§l길드 관리",
			"content" => "§l관리 항목을 선택해주세요.",
			"buttons" => [
				["text" => "나가기"],
				["text" => "§l길드원 강퇴하기\n길드원을 강퇴합니다."],
				["text" => "§l길드 상점\n길드 상점에 입장합니다."],
				["text" => "§l길드 가입신청 보기\n길드의 가입신청을 확인합니다."],
				["text" => "§l길드 폐쇄하기\n길드를 폐쇄합니다."],
				["text" => "§l길드 공동 리더 관리하기\n길드의 공동 리더를 관리합니다."],
				["text" => "§l길드 가입 조건 설정하기\n길드의 가입 조건을 설정합니다."],
				["text" => "§l길드 이름 변경하기\n길드 변경권을 사용해 길드 이름을 변경합니다."]
			]
		];
	}

	public function handleResponse(Player $player, $data) : void{
		if(is_int($data)){
			switch($data){
				case 1:
					$player->sendForm(new MemberForceQuitSelectForm($this->guild));
					break;
				case 2:
					$player->sendForm(new GuildShopSelectForm($this->guild));
					break;
				case 3:
					$player->sendForm(new InvitationListForm($this->guild));
					break;
				case 4:
					// TODO
					//GuildAPI::message($player, "해당 기능은 준비중입니다.");
					if($this->guild->getLeader() === GuildAPI::convert($player)){
						$player->sendForm(new GuildCloseForm($this->guild));
					}else{
						GuildAPI::message($player, "이 기능은 길드 리더만 사용 가능합니다.");
					}
					break;
				case 5:
					if($this->guild->getLeader() === GuildAPI::convert($player)){
						$player->sendForm(new GuildCoLeaderManageForm($this->guild));
					}else{
						GuildAPI::message($player, "이 기능은 길드 리더만 사용 가능합니다.");
					}
					break;
				case 6:
					$player->sendForm(new GuildJoinTypeSetForm($this->guild));
					break;
				case 7:
					if($this->guild->getLeader() === GuildAPI::convert($player)){
						$player->sendForm(new GuildNameChangeForm($this->guild));
					}else{
						GuildAPI::message($player, "이 기능은 길드 리더만 사용 가능합니다.");
					}
					break;
			}
		}
	}
}
