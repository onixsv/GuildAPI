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

use alvin0319\GuildAPI\event\EconomyEvent;
use alvin0319\GuildAPI\Guild;
use alvin0319\GuildAPI\GuildAPI;
use OnixUtils\OnixUtils;
use pocketmine\form\Form;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use function mb_strlen;
use function trim;

class GuildCreateForm implements Form{

	public function jsonSerialize() : array{
		$creatCost = GuildAPI::getInstance()->getConfig()->getNested("create-money");
		return [
			"type" => "custom_form",
			"title" => "§l길드 생성",
			"content" => [
				[
					"type" => "input",
					"text" => "길드의 이름을 입력해주세요.\n생성 비용: " . $creatCost
				]
			]
		];
	}

	public function handleResponse(Player $player, $data) : void{
		if(trim($data[0] ?? "") !== ""){
			if(mb_strlen($data[0]) > 8){
				OnixUtils::message($player, "길드 이름은 8자를 초과할 수 없습니다.");
				return;
			}
			if(!GuildAPI::getInstance()->getGuild($data[0]) instanceof Guild){
				$ev = new EconomyEvent($player, GuildAPI::getInstance()->getConfig()->getNested("create-money", 100000));
				$ev->call();
				if(!$ev->isCancelled()){
					GuildAPI::getInstance()->addGuild($player, TextFormat::clean($data[0]));
					GuildAPI::message($player, "길드를 생성했습니다.");
				}else{
					GuildAPI::message($player, "생성에 필요한 비용이 부족합니다.");
				}
			}else{
				GuildAPI::message($player, "해당 이름의 길드가 이미 존재합니다.");
			}
		}
	}
}