//
//  BillingAddressViewController.m
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 08/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "BillingAddressViewController.h"

@interface BillingAddressViewController ()

@end

@implementation BillingAddressViewController

- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    self.navigationItem.title = @"Billing Address";
    UIBarButtonItem *barBtn_save = [[UIBarButtonItem alloc]
                                    initWithTitle:@"Save"
                                    style:UIBarButtonItemStyleBordered
                                    target:self
                                    action:@selector(saveAction:)];
    self.navigationItem.rightBarButtonItem = barBtn_save;
    self.view.backgroundColor=kViewBackGroundColor;

}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

/*
#pragma mark - Navigation

// In a storyboard-based application, you will often want to do a little preparation before navigation
- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender {
    // Get the new view controller using [segue destinationViewController].
    // Pass the selected object to the new view controller.
}
*/
- (BOOL)textFieldShouldReturn:(UITextField *)textField{
    [self.view endEditing:YES];
    
    if (textField!=txt_zip){
        [[self.view viewWithTag:textField.tag+1] becomeFirstResponder];
        
        
    }
    else
    {
        // [self.backgroundScrollView setContentSize:CGSizeMake(320, 950)];
    }
    return [textField resignFirstResponder];
}
-(void)saveAction:(UIBarButtonItem *)sender{
    
    [self.navigationController popViewControllerAnimated:YES];
    
}
@end
