import React from 'react';
import Breadcrumbs from './Breadcrumbs';
import Button from '../inputs/Button';
import Icon from '../Icon';
import ActionMenuItem from '../ActionMenu/ActionMenuItem';
import ActionMenu from '../ActionMenu/ActionMenu';

const PageHeader = ({ title, image, icon, meta, breadcrumbs, actions = [], optionsVariant }) => {
	const filteredActions = actions.filter(action => !!action);

	return (
		<div className="py-6 bg-white border-b border-gray-300">
			<div className=" px-4 sm:px-6 md:px-8">
				<div className="lg:flex lg:items-center lg:justify-between">
					{image && <img src={image} alt={title} className="h-32 rounded-md mb-3 lg:mb-0 mr-6" />}
					<div className="flex-1 min-w-0">
						<Breadcrumbs breadcrumbs={breadcrumbs} />
						<h2 className="mt-2 text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
							{icon && <Icon icon={icon} type="solid" className="mr-2" />}
							<span>{title}</span>
						</h2>
						<div className="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-2 gap-2 sm:gap-6 text-sm sm:items-center text-gray-500">
							{meta}
						</div>
					</div>
					<div className="mt-5 flex sm:flex-row-reverse lg:mt-0 lg:ml-4">
						{filteredActions.map((action, key) => (
							<span className={key === 0 ? 'sm:ml-3' : 'hidden sm:block ml-3'} key={key}>
								{action.label ? (
									<Button
										href={action.url}
										onClick={action.onClick}
										size="sm"
										variant={action.variant}
										external={action.download}
										download={action.download}
									>
										<Icon icon={action.icon} mr />
										{action.label}
									</Button>
								) : (
									action
								)}
							</span>
						))}

						{/* Dropdown */}
						{filteredActions.length > 2 ? (
							<ActionMenu optionsVariant={optionsVariant}>
								{filteredActions.map(
									(action, key) =>
										key > 0 && (
											<ActionMenuItem
												key={key}
												url={action.url}
												onClick={action.onClick}
												download={action.download}
												variant={action.variant}
											>
												<Icon icon={action.icon} mr />
												{action.label}
											</ActionMenuItem>
										)
								)}
							</ActionMenu>
						) : (
							filteredActions.length === 2 && (
								<Button
									href={filteredActions[1].url}
									onClick={filteredActions[1].onClick}
									variant={filteredActions[1].variant}
									size="sm"
									className="ml-3 sm:hidden"
								>
									<Icon icon={filteredActions[1].icon} mr />
									{filteredActions[1].label}
								</Button>
							)
						)}
					</div>
				</div>
			</div>
		</div>
	);
};

export default PageHeader;
