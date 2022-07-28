import React from 'react';
import buttonStyles from "./inputs/buttonStyles";
import classNames from "../classNames";

const AvatarUpload = ({ currentImage, isSquare = true, updateFn }) => (
    <div className="mt-1 flex items-center">
        <span className={classNames('h-24 rounded-xl overflow-hidden bg-gray-100', isSquare ?? 'w-24')}>

            {currentImage
                ? <img src={currentImage} alt="Selected Profile Picture" className="h-full w-full" />
                : (
                  <svg className="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                  </svg>
                )
            }
        </span>
        <label htmlFor="avatar" className={buttonStyles('secondary', 'sm', false, 'ml-4')}>
            Change
        </label>
        <input type="file" id="avatar" className="hidden" onChange={e => updateFn(e.target.files[0])} />
    </div>
);

export default AvatarUpload;