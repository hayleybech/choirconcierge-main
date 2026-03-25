import React from 'react';
import Breadcrumbs from './Breadcrumbs';
import Button from '../inputs/Button';
import Icon from '../Icon';
import ActionMenuItem from '../ActionMenu/ActionMenuItem';
import ActionMenu from '../ActionMenu/ActionMenu';
import { PageHeading } from './PageHeading';

const PageHeader = ({ title, image, icon, meta, breadcrumbs, actions = [], optionsVariant }) => {
	const filteredActions = actions.filter(action => !!action);

	return (
		<div className="py-6 bg-white border-b border-gray-300">
			<div className=" px-4 sm:px-6 md:px-8">
				<div className="lg:flex lg:items-center lg:justify-between">
					{image && <img src={image} alt={title} className="h-32 rounded-md mb-3 lg:mb-0 mr-6" />}
					<div className="flex-1 min-w-0">
						<Breadcrumbs breadcrumbs={breadcrumbs} />
						<PageHeading>
							{icon && <Icon icon={icon} type="solid" className="mr-2" />}
							<span>{title}</span>
						</PageHeading>

						<div className="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-2 gap-2 sm:gap-6 text-sm sm:items-center text-gray-500">
							{meta}
						</div>
					</div>
					<div className="mt-5 flex sm:flex-row-reverse lg:mt-0 lg:ml-4">
						{/* Desktop */}
						{filteredActions.map((action, key) => (
							<span className="hidden sm:block ml-3" key={key}>
								{action.label ? (
									<Button
										href={action.url}
										onClick={action.onClick}
										size="sm"
										variant={action.variant}
										external={action.download}
										download={action.download}
										method={action.method}
										disabled={action.disabled}
									>
										<Icon icon={action.icon} mr />
										{action.label}
									</Button>
								) : (
									action
								)}
							</span>
						))}

						{/* Mobile - Always show first button */}
						{!!filteredActions[0]?.label ? (
							<Button
								href={filteredActions[0].url}
								onClick={filteredActions[0].onClick}
								variant={filteredActions[0].variant}
								method={filteredActions[0].method}
								disabled={filteredActions[0].disabled}
								size="sm"
								className="ml-3 sm:hidden"
							>
								<Icon icon={filteredActions[0].icon} mr />
								{filteredActions[0].label}
							</Button>
						) : (
							<div className="sm:hidden">{filteredActions[0]}</div>
						)}
						{/* Mobile - Show second button if exactly 2 buttons */}
						{filteredActions.length === 2 && (
							<>
								{!!filteredActions[1]?.label ? (
									<Button
										href={filteredActions[1].url}
										onClick={filteredActions[1].onClick}
										variant={filteredActions[1].variant}
										method={filteredActions[1].method}
										disabled={filteredActions[1].disabled}
										size="sm"
										className="ml-3 sm:hidden"
									>
										<Icon icon={filteredActions[1].icon} mr />
										{filteredActions[1].label}
									</Button>
								) : (
									<div className="sm:hidden">{filteredActions[1]}</div>
								)}
							</>
						)}

						{/* Mobile - Overflow Dropdown */}
						{filteredActions.length > 2 && (
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
												method={action.method}
												disabled={action.disabled}
											>
												<Icon icon={action.icon} mr />
												{action.label}
											</ActionMenuItem>
										)
								)}
							</ActionMenu>
						)}
					</div>
				</div>
			</div>
		</div>
	);
};

export default PageHeader;
