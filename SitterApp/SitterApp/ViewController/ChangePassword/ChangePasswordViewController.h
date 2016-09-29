//
//  ChangePasswordViewController.h
//  SitterApp
//
//  Created by Shilpa Gade on 03/06/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface ChangePasswordViewController : AppBaseViewController
@property (weak, nonatomic) IBOutlet UITextField *txt_CurrentPassword;
@property (weak, nonatomic) IBOutlet UITextField *txt_NewPassword;
@property (weak, nonatomic) IBOutlet UITextField *txt_ConfirmPassword;
@property(nonatomic,weak)Sitter *sitterInfo;
- (IBAction)on_Click_Done:(id)sender;

@end
