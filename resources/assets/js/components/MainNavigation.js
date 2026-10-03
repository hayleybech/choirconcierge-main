import {Link, usePage} from "@inertiajs/react";
import classNames from "../classNames";
import React from "react";
import Icon from "./Icon";
import useRoute from "../hooks/useRoute";

const MainNavigation = ({ navigation, closeSidebar, onOpenUserMenu }) => {
    const { route } = useRoute();
    const {user} = usePage().props;

    return (
		<nav className="flex min-h-0 flex-1 flex-col px-4">
			<div className="space-y-1">
				{navigation.map(item => (
					<div key={item.name}>
						<Link
							href={route(item.route)}
							className={classNames(
								item.active
									? 'bg-purple-800 text-white'
									: 'text-white/80 hover:bg-white/90 hover:text-brand-purple-dark',
								'group flex items-center px-2 py-2 text-base uppercase font-bold rounded-md'
							)}
							onClick={closeSidebar}
						>
							<Icon icon={item.icon} className="mr-4" />
							{item.name}
						</Link>
						{item.active && item.items.length > 0 && (
							<div className="flex flex-col mt-1 mb-3">
								{item.items.map(child => (
									<Link
										key={child.name}
										href={route(child.route)}
										className={classNames(
											child.active
											? 'font-semibold bg-black/25'
											: 'text-white/90 font-light hover:bg-white/90 hover:text-brand-purple-dark',
											'px-6 py-1.5 rounded-md text-base text-white'
										)}
										onClick={closeSidebar}
									>
										<Icon icon={child.icon} type="regular" className="mr-3" />
										{child.name}
									</Link>
								))}
							</div>
						)}
					</div>
				))}
			</div>
			{onOpenUserMenu && (
				<button
					type="button"
					onClick={onOpenUserMenu}
					className="mt-auto flex w-full shrink-0 items-center justify-between gap-3 rounded-md py-2 text-left text-white"
				>
					<div className="flex items-center gap-3">
						<img className="h-10 w-10 shrink-0 rounded-lg" src={user.avatar_url} alt={user.name} />
						<span className="min-w-0">
							<span className="block truncate text-sm font-semibold">{user.name}</span>
							<span className="block truncate text-xs text-white/75">{user.email}</span>
						</span>
					</div>

					<Icon icon="arrow-right" type="light" />
				</button>
			)}
		</nav>
	);
}

export default MainNavigation;
