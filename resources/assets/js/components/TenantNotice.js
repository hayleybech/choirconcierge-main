import React, { useState } from "react";
import classNames from "../classNames";
import Icon from "./Icon";
import Button from "./inputs/Button";

const TenantNotice = ({ variant, children }) => {
  const [showNotices, setShowNotices] = useState(true);

  if(!showNotices) {
    return false;
  }

  return (
    <div className={classNames(
        variant === 'info' && 'bg-blue-500',
        variant === 'warning' && 'bg-orange-500',
        variant === 'danger' && 'bg-red-500',
        'text-white py-2 md:py-1 px-4 text-center text-sm font-bold flex justify-center',
      )}>
      <div className="grow flex flex-col md:flex-row gap-x-6 gap-y-2 items-center justify-center">
        {children}
      </div>
      <Button variant="clear" size="xs" onClick={() => setShowNotices(false)}>
        <Icon icon="times" className="text-white" />
      </Button>
    </div>
  );
}

export default TenantNotice;