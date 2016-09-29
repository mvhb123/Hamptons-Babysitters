//
//  ChangePasswordViewController.m
//  SitterApp
//
//  Created by Shilpa Gade on 03/06/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "ChangePasswordViewController.h"

@interface ChangePasswordViewController ()

@end

@implementation ChangePasswordViewController
@synthesize sitterInfo;
- (void)viewDidLoad {
    [super viewDidLoad];
    self.sitterInfo=[ApplicationManager getInstance].sitterInfo;
    UIBarButtonItem *barBtn_save = [[UIBarButtonItem alloc]
                                    initWithTitle:@"Save"
                                    style:UIBarButtonItemStyleBordered
                                    target:self
                                    action:@selector(on_Click_Done:)];
    self.navigationItem.rightBarButtonItem = barBtn_save;
    // Do any additional setup after loading the view from its nib.
}
-(void)viewWillAppear:(BOOL)animated{
    [super viewWillAppear:animated];
     self.navigationItem.title=@"Change Password";
    self.view.backgroundColor=kBackgroundColor;
}
- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark- UITextFieldDelegate
- (BOOL)textFieldShouldReturn:(UITextField *)textField
{
    if (textField == self.txt_CurrentPassword) {
        [self.txt_NewPassword becomeFirstResponder];
    }else if (textField == self.txt_NewPassword) {
        [self.txt_ConfirmPassword becomeFirstResponder];
    }else if (textField==self.txt_ConfirmPassword)
        [textField resignFirstResponder];
    else
        [textField resignFirstResponder];
    return true;
}

#pragma mark-UserDefineMethods
/**
 This method is used to change password
 @param id sender
 @return void
 */
- (IBAction)on_Click_Done:(id)sender {
    if ([self checkValidation])
    {
         [self.txt_ConfirmPassword resignFirstResponder];
         [self.txt_CurrentPassword  resignFirstResponder];
         [self.txt_NewPassword  resignFirstResponder];
        [self startNetworkActivity:NO];
        NSMutableDictionary *dict_CngPwd=[[NSMutableDictionary alloc] init];
        [dict_CngPwd setSafeObject:self.sitterInfo.sitterId forKey:kUserId];
        [dict_CngPwd setSafeObject:self.sitterInfo.str_TokenData forKey:kToken];
        [dict_CngPwd setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
        [dict_CngPwd setSafeObject:self.txt_CurrentPassword.text forKey:kCurrentPassword];
        [dict_CngPwd setSafeObject:self.txt_ConfirmPassword.text forKey:kPassword];
        SMCoreNetworkManager *networkManager;
        networkManager= [[SMCoreNetworkManager alloc] initWithBaseURLString:kChangePassword_API];
        networkManager.delegate = self;
        [networkManager changePassword:dict_CngPwd  forRequestNumber:0];
    }
}

/**
 This method is used to check validations
 @param nill
 @return BOOL
 */
-(BOOL)checkValidation
{
    if (trimedString(self.txt_CurrentPassword.text).length==0)
    {
        [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:kEnterCurrentPassword];
        return false;
    }
    else if (trimedString(self.txt_CurrentPassword.text).length<6)
    {
        [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:kPasswordLength];
        return false;
    }
    else if (trimedString(self.txt_NewPassword.text).length==0)
    {
        [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:kEnterNewPassword];
        return false;
    }
    else if (trimedString(self.txt_NewPassword.text).length<6)
    {
        [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:kPasswordLength];
        return false;
    }
    else if (trimedString(self.txt_ConfirmPassword.text).length==0)
    {
        [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:kEnterConfrmPassword];
        return false;
        
    }
    else if (trimedString(self.txt_ConfirmPassword.text).length<6)
    {
        [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:kPasswordLength];
        return false;
        
    }
    else if (![trimedString(self.txt_NewPassword.text) isEqualToString:trimedString(self.txt_ConfirmPassword.text)])
    {
        [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:kPasswordNotMatch];
        return false;
    }
    else
        return true;
}
#pragma mark - AlertView delegate
- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex
{
    if (alertView.tag==100) {
        [self.navigationController popViewControllerAnimated:YES];
    }
    [self appDidLogout:alertView clickedButtonAtIndex:buttonIndex];

    
}
#pragma mark - SMCoreNetworkManagerDelegate

- (void)requestDidSucceedWithResponseObject:(id)responseObject
                                   withTask:(NSURLSessionDataTask *)task
                              withRequestId:(NSUInteger)requestId{
    [self stopNetworkActivity];
    
    NSDictionary *dict_responseObj=responseObject;
    if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess])
    {
        //Show Alert with title and message only
        UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"" message:[dict_responseObj safeObjectForKey:kMessage] delegate:self cancelButtonTitle:@"OK" otherButtonTitles:nil];
        [alert setTag:100];
        [alert show];
    }
    else
    {
        [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
    }
}

- (void)requestDidFailWithErrorObject:(id)error withRequestId:(NSUInteger)requestId{
    // NSError *errorcode=(NSError *)error;
    [self stopNetworkActivity];
    [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[error localizedDescription] ];
    
}

@end
