//
//  EmergencyContactViewController.m
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 03/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "EmergencyContactViewController.h"

@interface EmergencyContactViewController ()

@end

@implementation EmergencyContactViewController

- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    [[self navigationController] setNavigationBarHidden:NO animated:YES];
    self.navigationItem.title = @"Emergency Contact";
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
    if (textField == txt_name) {
        [txt_phone becomeFirstResponder];
    }
    else
    {
        [textField resignFirstResponder];
    }
    return true;
}


-(void)saveAction:(UIBarButtonItem *)sender{
    
    
    [self.navigationController popViewControllerAnimated:YES];
    
}

@end
