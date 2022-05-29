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

namespace alvin0319\GuildAPI\form\noGuild;

use alvin0319\GuildAPI\form\all\GuildRankForm;
use pocketmine\form\Form;
use pocketmine\player\Player;
use function is_null;

class GuildMainForm implements Form{

	public function jsonSerialize() : array{
		return [
			"type" => "form",
			"title" => "§l길드 메뉴",
			"content" => "§l아직 소속된 길드가 없습니다.",
			"buttons" => [
				["text" => "나가기"],
				["text" => "§l길드 생성하기\n길드를 생성합니다."],
				["text" => "§l길드 찾아보기\n길드를 찾아봅니다."],
				["text" => "§l길드 순위 보기\n길드 순위를 확인합니다."]
			]
		];
	}

	public function handleResponse(Player $player, $data) : void{
		if(!is_null($data)){
			switch($data){
				case 1:
					$player->sendForm(new GuildCreateForm());
					break;
				case 2:
					$player->sendForm(new GuildSearchForm());
					break;
				case 3:
					$player->sendForm(new GuildRankForm());
					break;
			}
		}
	}
}