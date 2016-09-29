//
//  ChangePasswordViewController.h
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 09/06/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface ChangePasswordViewController : AppBaseViewController
{
    __weak IBOutlet UITextField *txt_newPassword;
    __weak IBOutlet UITextField *txt_currentPassword;
    
    __weak IBOutlet UITextField *txt_confirmPAssword;
}
@property(nonatomic,assign)Parent *parentInfo;
@end
