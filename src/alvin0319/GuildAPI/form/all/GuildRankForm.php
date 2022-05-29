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
use function array_keys;
use function arsort;
use function is_int;

class GuildRankForm implements Form{

	protected array $buttons = [];

	public function jsonSerialize() : array{
		$arr = [];
		foreach(GuildAPI::getInstance()->getGuilds() as $guild){
			$arr[$guild->getName()] = $guild->getGuildStorage()->getGuildPoints();
		}
		arsort($arr);
		$final = [];
		foreach($arr as $name => $point){
			$final[] = ["text" => "{$name} 길드\n포인트: {$point}"];
		}
		$arr = array_keys($arr);
		$this->buttons = $arr;
		return [
			"type" => "form",
			"title" => "§l길드 순위",
			"content" => "§l길드 이름을 클릭하시면 해당 길드의 정보 창으로 이동합니다!",
			"buttons" => $final
		];
	}

	public function handleResponse(Player $player, $data) : void{
		if(is_int($data)){
			if(isset($this->buttons[$data])){
				$guild = GuildAPI::getInstance()->getGuild($this->buttons[$data]);
				if($guild instanceof Guild){
					$player->sendForm(new GuildInfoForm($player, $guild));
				}
			}
		}
	}
}
