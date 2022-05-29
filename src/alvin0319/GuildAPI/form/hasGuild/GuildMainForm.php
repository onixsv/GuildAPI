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

namespace alvin0319\GuildAPI\form\hasGuild;

use alvin0319\GuildAPI\form\all\GuildInfoForm;
use alvin0319\GuildAPI\form\all\GuildRankForm;
use alvin0319\GuildAPI\form\hasGuild\manage\GuildManageForm;
use alvin0319\GuildAPI\Guild;
use alvin0319\GuildAPI\GuildAPI;
use pocketmine\form\Form;
use pocketmine\player\Player;
use function is_int;
use function strtolower;

class GuildMainForm implements Form{

	/** @var Guild */
	protected Guild $guild;

	/** @var Player */
	protected Player $player;

	public function __construct(Player $player, Guild $guild){
		$this->player = $player;
		$this->guild = $guild;
	}

	public function jsonSerialize() : array{
		$data = [
			"type" => "form",
			"title" => "§l{$this->guild->getName()} 길드",
			"content" => "",
			"buttons" => [
				["text" => "나가기"],
				["text" => "§l길드 채팅\n길드 채팅을 관리합니다."],
				["text" => "§l길드 정보 보기\n길드 정보를 확인합니다."],
				["text" => "§l길드 순위 보기\n길드 순위를 확인합니다."],
				["text" => "§l길드 탈퇴하기\n길드를 탈퇴합니다."]
			]
		];
		if($this->guild->hasPermission($this->player)){
			$data["buttons"][] = ["text" => "§l길드 관리\n길드를 관리합니다."];
		}
		return $data;
	}

	public function handleResponse(Player $player, $data) : void{
		if(is_int($data)){
			switch($data){
				case 1:
					if(GuildAPI::getInstance()->getEventListener()->isGuildChat($player)){
						GuildAPI::getInstance()->getEventListener()->removeGuildChat($player);
						GuildAPI::message($player, "길드 채팅을 비활성화 했습니다.");
					}else{
						GuildAPI::getInstance()->getEventListener()->addGuildChat($player);
						GuildAPI::message($player, "길드 채팅을 활성화 했습니다.");
					}
					break;
				case 2:
					$player->sendForm(new GuildInfoForm($player, $this->guild));
					break;
				case 3:
					$player->sendForm(new GuildRankForm());
					break;
				case 4:
					if($this->guild->getLeader() === strtolower($player->getName())){
						GuildAPI::message($player, "길드 리더는 길드를 탈퇴할 수 없습니다.");
					}else{
						$player->sendForm(new GuildQuitForm($player, $this->guild));
					}
					break;
				case 5:
					if($this->guild->hasPermission($player)){
						$player->sendForm(new GuildManageForm($this->guild));
					}
					break;
			}
		}
	}
}
