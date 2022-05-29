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

namespace alvin0319\GuildAPI\commands;

use alvin0319\GuildAPI\Guild;
use alvin0319\GuildAPI\GuildAPI;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

class GuildManageCommand extends Command implements PluginOwned, CommandExecutor{

	public function __construct(){
		parent::__construct("길드관리", "길드를 관리합니다.");
		$this->setPermission("guildapi.command.manage");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
		return $this->onCommand($sender, $this, $commandLabel, $args);
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		if(!$this->testPermissionSilent($sender))
			return false;
		switch($args[0] ?? "x"){
			case "길포설정":
				if(trim($args[1] ?? "") !== ""){
					if(($guild = GuildAPI::getInstance()->getGuild($args[1])) instanceof Guild){
						if(trim($args[2] ?? "") !== ""){
							if(is_numeric($args[2]) && intval($args[2]) >= 0){
								$guild->getGuildStorage()->setGuildPoints(intval($args[2]));
								GuildAPI::message($sender, "{$guild->getName()} 길드의 포인트를 {$args[2]}(으)로 설정했습니다.");
							}else{
								GuildAPI::message($sender, "/길드관리 길포설정 [길드] [포인트] - 길드의 포인트를 설정합니다.");
							}
						}else{
							GuildAPI::message($sender, "/길드관리 길포설정 [길드] [포인트] - 길드의 포인트를 설정합니다.");
						}
					}else{
						GuildAPI::message($sender, "해당 이름의 길드가 존재하지 않습니다.");
					}
				}else{
					GuildAPI::message($sender, "/길드관리 길포설정 [길드] [포인트] - 길드의 포인트를 설정합니다.");
				}
				break;
			case "리더변경":
				if(trim($args[1] ?? "") !== ""){
					if(($guild = GuildAPI::getInstance()->getGuild($args[1])) instanceof Guild){
						if(trim($args[2] ?? "") !== ""){
							if($guild->isMember($args[2])){
								$leader = $guild->getLeader();
								$guild->setRole($args[2], Guild::LEADER);
								$guild->setRole($leader, Guild::MEMBER);
								GuildAPI::message($sender, "성공적으로 리더를 변경하였습니다.");
							}else{
								GuildAPI::message($sender, "해당 유저는 해당 길드의 길드원이 아닙니다.");
							}
						}else{
							GuildAPI::message($sender, "/길드관리 리더변경 [길드] [새로운리더] - 길드의 리더를 변경합니다.");
						}
					}else{
						GuildAPI::message($sender, "해당 이름의 길드가 존재하지 않습니다.");
					}
				}else{
					GuildAPI::message($sender, "/길드관리 리더변경 [길드] [새로운리더] - 길드의 리더를 변경합니다.");
				}
				break;
			case "길드폐쇄":
				if(trim($args[1] ?? "") !== ""){
					if(($guild = GuildAPI::getInstance()->getGuild($args[1])) instanceof Guild){
						GuildAPI::getInstance()->removeGuild($guild);
						GuildAPI::message($sender, "{$guild->getName()} 길드를 폐쇄했습니다.");
					}else{
						GuildAPI::message($sender, "해당 이름의 길드가 존재하지 않습니다.");
					}
				}else{
					GuildAPI::message($sender, "/길드관리 길드폐쇄 [길드] - 길드를 폐쇄합니다.");
				}
				break;
			default:
				GuildAPI::message($sender, "/길드관리 길포설정 [길드] [포인트] - 길드의 포인트를 설정합니다.");
				GuildAPI::message($sender, "/길드관리 리더변경 [길드] [새로운리더] - 길드의 리더를 변경합니다.");
				GuildAPI::message($sender, "/길드관리 길드폐쇄 [길드] - 길드를 폐쇄합니다.");
		}
		return true;
	}

	/**
	 * @return GuildAPI
	 */
	public function getOwningPlugin() : Plugin{
		return GuildAPI::getInstance();
	}
}