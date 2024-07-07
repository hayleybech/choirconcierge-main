import React from "react";
import useRoute from "../hooks/useRoute";
import Icon from "../components/Icon";
import Button from "../components/inputs/Button";
import {usePage} from "@inertiajs/react";
import StyledLink from "../components/StyledLink";

export const OuterPageErrorFallback = ({ error, resetErrorBoundary }) => {
	const {route} = useRoute();
	const { tenant } = usePage().props;

	return (
		<div className="grid min-h-full place-items-center bg-white px-6 py-24 sm:py-32 lg:px-8">
			<div className="text-center">
				<p className="text-base font-semibold text-purple-600">Oops!</p>
				<h1 className="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-5xl">An error occurred</h1>
				<p className="mt-6 text-base leading-7 text-gray-500">Sorry, this page couldn't recover from an issue
					that occurred. </p>
				<p className="mt-6 text-base leading-7">Try refreshing the page, or come back to this page later. If the issue happens again, please contact us.</p>
				<div className="mt-10 flex items-center justify-center gap-x-6">
					<Button href={tenant ? route('dash') : route('central.dash')} variant="primary">
						Go to dashboard
						<Icon icon="long-arrow-right" />
					</Button>
					<Button href="mailto:hello@choirconcierge.com" target="_blank" external variant="secondary">
						<Icon icon="envelope" />
						Email Us
					</Button>
				</div>

				<div className="flex-shrink-0 bg-gray-50">
					<div className="mx-auto max-w-7xl w-full px-4 py-16 sm:px-6 lg:px-8">
						<nav className="flex space-x-4 justify-center">
							<StyledLink href="mailto:hello@choirconcierge.com" target="_blank" variant="secondary">
								Email Us
							</StyledLink>
							<span className="inline-block border-l border-gray-300" aria-hidden="true"/>
							<StyledLink href="https://stats.uptimerobot.com/99Y2MUmKA5" target="_blank" variant="secondary">
								Status
							</StyledLink>
							<span className="inline-block border-l border-gray-300" aria-hidden="true"/>
							<StyledLink href="https://twitter.com/ChoirConcierge" target="_blank" variant="secondary">
								Twitter
							</StyledLink>
							<span className="inline-block border-l border-gray-300" aria-hidden="true"/>
							<StyledLink href="https://www.facebook.com/choirconcierge" target="_blank" variant="secondary">
								Facebook
							</StyledLink>
						</nav>
					</div>
				</div>
			</div>
		</div>
	);
}

export default OuterPageErrorFallback;