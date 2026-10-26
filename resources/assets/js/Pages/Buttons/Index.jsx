import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import AppHead from '../../components/AppHead';
import ButtonV2 from '../../components/inputs/ButtonV2';
import Panel from '../../components/Panel';
import PageTopBar, { PageTopNavigation } from '../../components/PageTopBar';
import { PageHeader, PageHeaderContent, PageHeaderTitle } from '../../components/PageHeader/PageHeader';
import Icon from '../../components/Icon';
 
const v2Intents = ['primary', 'secondary', 'danger', 'warning', 'success'];
const v2Weights = ['solid', 'outline', 'clear'];
const v2Sizes = ['lg', 'md', 'sm'];
const v2Themes = ['light', 'dark'];

const Index = ({ setSidebarOpen }) => (
	<>
		<AppHead title="Buttons" />
		<PageTopBar setSidebarOpen={setSidebarOpen}>
			<PageTopNavigation title="Buttons" />
		</PageTopBar>
		<PageHeader>
			<PageHeaderContent>
				<PageHeaderTitle>Buttons</PageHeaderTitle>
			</PageHeaderContent>
		</PageHeader>

		<div className="py-6">
			<div className="mx-auto px-4 sm:px-6 lg:px-16">
				<Panel>
					<div className="flex flex-col gap-y-8">
						<div>
							<h2 className="text-lg font-semibold text-gray-900">Button v2</h2>
							<p className="mt-1 text-sm text-gray-500">
								Colors, weights, and sizes available in the new button API.
							</p>
						</div>
						{v2Themes.map(theme => (
							<div key={theme} className={theme === 'dark' ? 'rounded-lg bg-gray-800 p-6' : ''}>
								<h3
									className={`mb-3 text-sm font-semibold capitalize ${
										theme === 'dark' ? 'text-gray-100' : 'text-gray-700'
									}`}
								>
									{theme} theme
								</h3>
								{v2Weights.map(weight => (
									<div key={weight} className="overflow-x-auto">
										<h3
											className={`mb-3 text-sm font-semibold capitalize ${
												theme === 'dark' ? 'text-gray-100' : 'text-gray-700'
											}`}
										>
											{weight}
										</h3>
										<div className="flex justify-between flex-wrap items-center gap-3">
											{v2Intents.map(intent => (
												<div key={intent} className="flex flex-col items-center gap-3">
													{v2Sizes.map(size => (
														<ButtonV2
															key={`${intent}-${size}`}
															intent={intent}
															weight={weight}
															size={size}
															theme={theme}
															type="button"
														>
															<Icon icon="plus" />
															{intent} {size}
														</ButtonV2>
													))}
												</div>
											))}
										</div>
									</div>
								))}
							</div>
						))}
					</div>
				</Panel>
			</div>
		</div>
	</>
);

Index.layout = page => <TenantLayout children={page} />;

export default Index;
