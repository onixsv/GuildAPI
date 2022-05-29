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

namespace alvin0319\GuildAPI;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use pocketmine\Server;
use function is_string;
use function trim;

class EventListener implements Listener{

	protected array $guildChat = [];

	public function addGuildChat(Player $player) : void{
		$this->guildChat[$player->getName()] = true;
	}

	public function removeGuildChat(Player $player) : void{
		unset($this->guildChat[$player->getName()]);
	}

	public function isGuildChat(Player $player) : bool{
		return isset($this->guildChat[$player->getName()]);
	}

	public function onPlayerJoin(PlayerJoinEvent $event) : void{
		$player = $event->getPlayer();
		if(GuildAPI::getInstance()->hasGuild($player)){
			$guild = GuildAPI::getInstance()->getGuildByPlayer($player);
			$guild->broadcastMessage("길드 {$guild->getRole($player)} {$player->getName()}님이 접속하셨습니다.");
			if($guild->getGuildStorage()->getAllowNameTag()){
				$player->setNameTag($player->getNameTag() . "\n§r§7소속된 길드: {$guild->getName()}");
			}
			if(is_string($announce = $guild->getGuildStorage()->getAnnounce())){
				if(trim($announce) !== ""){
					$player->sendMessage("§a==========");
					GuildAPI::message($player, $announce);
					$player->sendMessage("§a==========");
				}
			}
		}
	}

	public function onPlayerQuit(PlayerQuitEvent $event) : void{
		$player = $event->getPlayer();
		if(GuildAPI::getInstance()->hasGuild($player)){
			$guild = GuildAPI::getInstance()->getGuildByPlayer($player);
			$guild->broadcastMessage("길드 {$guild->getRole($player)} {$player->getName()}님이 퇴장하셨습니다.");
		}
	}

	public function onPlayerChat(PlayerChatEvent $event) : void{
		$player = $event->getPlayer();
		if($this->isGuildChat($player)){
			if(($guild = GuildAPI::getInstance()->getGuildByPlayer($player)) instanceof Guild){
				$guild->broadcastMessage($event->getMessage(), $player->getName());
				$event->cancel();
				foreach($player->getServer()->getOnlinePlayers() as $op){
					if($op->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
						if(!$guild->isMember($op)){
							$op->sendMessage("§b§l[{$guild->getName()}]§r§7 " . $player->getName() . " > " . $event->getMessage());
						}
					}
				}
				Server::getInstance()->getLogger()->info("§b§l[{$guild->getName()}]§r§7 " . $player->getName() . " > " . $event->getMessage());
			}else{
				$this->removeGuildChat($player);
			}
		}
	}
}
