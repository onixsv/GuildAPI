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

use alvin0319\GuildAPI\Guild;
use alvin0319\GuildAPI\GuildAPI;
use OnixUtils\OnixUtils;
use pocketmine\form\Form;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use function array_shift;
use function count;
use function is_array;
use function trim;

class GuildNameChangeForm implements Form{
	/** @var Guild */
	protected Guild $guild;

	public function __construct(Guild $guild){
		$this->guild = $guild;
	}

	public function jsonSerialize() : array{
		return [
			"type" => "custom_form",
			"title" => "§l길드 관리",
			"content" => [
				[
					"type" => "input",
					"text" => "길드 이름을 입력해주세요\n길드 이름은 최대 8글자 까지 가능합니다."
				]
			]
		];
	}

	public function handleResponse(Player $player, $data) : void{
		if(!is_array($data) || count($data) !== 1){
			return;
		}
		$guildName = array_shift($data);
		if(trim($guildName ?? "") === ""){
			OnixUtils::message($player, "길드 이름을 입력해주세요.");
			return;
		}
		if(GuildAPI::getInstance()->getGuild($guildName) !== null){
			OnixUtils::message($player, "해당 이름의 길드가 이미 존재합니다.");
			return;
		}
		$guildItem = ItemFactory::getInstance()->get(ItemIds::PAPER, 12);
		if(!$player->getInventory()->contains($guildItem)){
			OnixUtils::message($player, "길드 이름 변경권을 소유하고 있지 않습니다.");
			return;
		}
		$player->getInventory()->removeItem($guildItem);
		GuildAPI::getInstance()->changeGuildName($this->guild, TextFormat::clean($guildName));
		OnixUtils::message($player, "성공적으로 길드 이름을 변경했습니다.");
	}
}