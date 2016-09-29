//
//  ChangePasswordViewController.m
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 09/06/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "ChangePasswordViewController.h"

@interface ChangePasswordViewController ()

@end

@implementation ChangePasswordViewController

- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    self.navigationItem.title = @"Change Password";
    UIBarButtonItem *barBtn_save = [[UIBarButtonItem alloc]
                                    initWithTitle:@"Save"
                                    style:UIBarButtonItemStyleBordered
                                    target:self
                                    action:@selector(saveAction:)];
    self.navigationItem.rightBarButtonItem = barBtn_save;
    self.parentInfo = [ApplicationManager getInstance].parentInfo;
    self.view.backgroundColor=kViewBackGroundColor;
    txt_newPassword.textColor=kColorGrayDark;
    txt_confirmPAssword.textColor=kColorGrayDark;
    txt_currentPassword.textColor=kColorGrayDark;
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}
- (BOOL)textFieldShouldReturn:(UITextField *)textField{
    [self.view endEditing:YES];
    if (textField!=txt_confirmPAssword){
        [[self.view viewWithTag:textField.tag+1] becomeFirstResponder];
    }
    else
    {
      return [textField resignFirstResponder];
    }
    
    return [textField resignFirstResponder];
}
-(void)saveAction:(UIBarButtonItem *)sender{
    [self.view endEditing:YES];
    if ([self checkUserData]) {
        NSMutableDictionary *dict_changePassword = [[NSMutableDictionary alloc]init];
        [dict_changePassword setSafeObject:self.parentInfo.parentUserId forKey:kUserId];
        [dict_changePassword setSafeObject:self.parentInfo.tokenData forKey:kToken];
        [dict_changePassword setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
        [dict_changePassword setSafeObject:txt_newPassword.text forKey:kPassword];
        [dict_changePassword setSafeObject:txt_currentPassword.text forKey:kCurrentPassword];
        [dict_changePassword setObject:@"P" forKey:kUserType];
        [self startNetworkActivity:NO];
        SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kChangePasswordAPI];
        DDLogInfo(@"%@",kChangePasswordAPI);
        networkManager.delegate = self;
        [networkManager changePassword:dict_changePassword forRequestNumber:1];
    }
}
-(BOOL)checkUserData
{
    BOOL isvalid = NO;
    txt_currentPassword.text = trimedString(txt_currentPassword.text);
    txt_newPassword.text = trimedString(txt_newPassword.text);
    txt_confirmPAssword.text = trimedString(txt_confirmPAssword.text);
    if ([txt_currentPassword.text isEqualToString:@""])
    {
       [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterCurrentPassword];
        isvalid=NO;
    }
    else if ([txt_currentPassword.text length]<6)
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kCurrentPasswordLength];
        isvalid=NO;
    }
      else if ([txt_newPassword.text isEqualToString:@""]) {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterNewPassword];
        isvalid=NO;
    }
    else if ([txt_newPassword.text length]<6)
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kNewPasswordLength];
        isvalid=NO;
    }

    else if ([txt_confirmPAssword.text isEqualToString:@""])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterConfrmPassword];
        isvalid=NO;
    }
    else if ([txt_confirmPAssword.text length]<6)
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kConfirmPasswordLength];
        isvalid=NO;
    }

    else if (![txt_newPassword.text isEqualToString:txt_confirmPAssword.text])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kPasswordNotMatch];
        isvalid=NO;
    }
    else
        isvalid = YES;
    return isvalid;
}
#pragma mark - SMCoreNetworkManagerDelegate
- (void)requestDidSucceedWithResponseObject:(id)responseObject
                                   withTask:(NSURLSessionDataTask *)task
                              withRequestId:(NSUInteger)requestId{
    NSDictionary *dict_responseObj=responseObject;
    [self stopNetworkActivity];
    DDLogInfo(@"%@",responseObject);
    switch (requestId) {
        case 1:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
            [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kMessage]];
                self.parentInfo.tokenData = [[dict_responseObj safeObjectForKey:kData]safeObjectForKey:kTokenData];
            [self.navigationController popViewControllerAnimated:YES];
            }
            else
            {
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
            }
            
            break;
    }
}
- (void)requestDidFailWithErrorObject:(id)error withRequestId:(NSUInteger)requestId{
    [self stopNetworkActivity];
    DDLogInfo(@"%@",error);
    [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[error localizedDescription] ];
    
}
@end
