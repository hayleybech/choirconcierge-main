import React from 'react';
import Icon from "./Icon";
import Button from "./inputs/Button";

const EmptyState = ({ title, description, actionDescription, icon, href, actionLabel, actionIcon }) => (
    <div className="text-center px-5 py-10 md:py-16">
      {icon && <Icon icon={icon} type="regular" className="text-5xl" />}

        <h3 className="mt-2 text-lg font-medium text-gray-900">{title}</h3>

        {description && <p className="mt-1 text-md text-gray-500">{description}</p>}
        {actionDescription && <p className="mt-1 text-md text-gray-500">{actionDescription}</p>}

        {href && (
            <div className="mt-6">
                <Button href={href} variant="primary">
                    <Icon icon={actionIcon} aria-hidden="true" />
                    {actionLabel}
                </Button>
            </div>
        )}
    </div>
);

export default EmptyState;