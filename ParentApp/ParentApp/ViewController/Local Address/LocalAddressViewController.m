//
//  LocalAddressViewController.m
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 03/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//
#import "LocalAddressViewController.h"
#import "RegistrationViewController.h"
#import "EditProfileViewController.h"

@interface LocalAddressViewController ()
@end
@implementation LocalAddressViewController
{
}
@synthesize dict_profileData,dropDown;
- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    tbl_stateList.hidden = true;
    tbl_stateList.layer.borderWidth = 0.5;
    tbl_stateList.layer.borderColor = [UIColor grayColor].CGColor;
    array_statelist = [[NSMutableArray alloc]init];
    array_states =    [[NSMutableArray alloc]init];
    self.parentInfo = [ApplicationManager getInstance].parentInfo;
    [self.backgroundScrollView removeGestureRecognizer:self.tapRecognizer];
    self.navigationItem.title = @"Local Address";
    NavigationBarRightButton;
    DDLogInfo(@"local address is %@",dict_profileData);
    array_statelist = [ApplicationManager getInstance].array_stateList;
    if (array_statelist.count > 0) {
        DDLogInfo(@"State list is %@",array_statelist);
        [tbl_stateList reloadData];
    }
    else
    {
        NSMutableDictionary *dict_countryId = [[NSMutableDictionary alloc]init];
        [dict_countryId setSafeObject:@"223" forKey:kCountryId];
        [dict_countryId setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
        [self startNetworkActivity:YES];
        SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kStateListAPI];
        DDLogInfo(@"%@",kStateListAPI);
        networkManager.delegate = self;
        [networkManager getStateList:dict_countryId forRequestNumber:1];
    }
    [self setDataInFields];
    [self setFontTypeAndFontSize];
    self.view.backgroundColor=kViewBackGroundColor;
    NSString *strState=[[NSUserDefaults standardUserDefaults] objectForKey:kstateKey];
    [txt_state setText:strState];
    if ([strState isEqualToString:kNewYork]) {
        str_stateId=@"3655";
    }else{
        str_stateId=@"3624";//For California
    }

}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}
- (void)keyboardWillHide:(NSNotification *)n
{
//    [self.view endEditing:YES];
//    [self.backgroundScrollView setContentOffset:CGPointMake(0, 0)];
//    contentHight = 0;
//    UIView *lLast = [view_localAddress.subviews lastObject];
//    NSInteger wd = lLast.frame.origin.y;
//    NSInteger ht = lLast.frame.size.height;
//    contentHight = wd+ht;
//    self.backgroundScrollView.contentSize = CGSizeMake(self.backgroundScrollView.frame.size.width,contentHight+50);
}
-(void)viewDidLayoutSubviews
{
    [super viewDidLayoutSubviews];
    contentHight = 0;
    UIView *lLast = [view_localAddress.subviews lastObject];
    NSInteger wd = lLast.frame.origin.y;
    NSInteger ht = lLast.frame.size.height;
    contentHight = wd+ht;
    self.backgroundScrollView.contentSize = CGSizeMake(self.backgroundScrollView.frame.size.width,contentHight+50);
}


- (void)textFieldDidBeginEditing:(UITextField *)textField{
    
    if (textField == txt_state) {
    }else{
        tbl_stateList.hidden = true;
    }
}
- (BOOL)textFieldShouldReturn:(UITextField *)textField{
    tbl_stateList.hidden = true;
    [self.view endEditing:YES];
    if (textField ==txt_city) {
        [txt_zip becomeFirstResponder];
    }
    if (textField!=txt_hotel_name){
        [[self.view viewWithTag:textField.tag+1] becomeFirstResponder];
    }
    else
    {
        self.backgroundScrollView.contentSize = CGSizeMake(self.backgroundScrollView.frame.size.width,contentHight+50);
    }
    return [textField resignFirstResponder];
}
#pragma mark-UserDefineMethods
-(void)setFontTypeAndFontSize
{
    lbl_hotelName.textColor=kLabelColor;
    lbl_city.textColor=kLabelColor;
    lbl_crossStreet.textColor=kLabelColor;
    lbl_state.textColor=kLabelColor;
    lbl_streetAddress.textColor=kLabelColor;
    lbl_zip.textColor=kLabelColor;
    
}
-(void)saveAction:(UIBarButtonItem *)sender{
    [self.view endEditing:YES];
    if (checkValue == 1) {
        //if (![self.parentInfo.profileStatus isEqualToString:@"1"]) {
            if ([self checkUserData]) {
            NSArray *array = [self.navigationController viewControllers];
            EditProfileViewController *editView =  self.navigationController.viewControllers[[array count]-2];
            NSMutableDictionary *dict_localAddress = [[NSMutableDictionary alloc]init];
            [dict_localAddress setSafeObject:txt_hotel_name.text forKey:kHotelName];
            [dict_localAddress setSafeObject:txt_crossStreet.text forKey:kCrossStreet];
            [dict_localAddress setSafeObject:txt_street_address.text forKey:kStreetAddress];
            [dict_localAddress setSafeObject:txt_city.text forKey:kCity];
            [dict_localAddress setSafeObject:txt_state.text forKey:@"stateName"];
            [dict_localAddress setSafeObject:str_stateId forKey:kState];
            [dict_localAddress setSafeObject:txt_zip.text forKey:kZip];
            [dict_localAddress setSafeObject:klocal forKey:kAddressType];
            if (dict_profileData.count!=0) {
                [dict_localAddress setSafeObject:[dict_profileData safeObjectForKey:kAddressId] forKey:kAddressId];
            }
            else
                [dict_localAddress setSafeObject:str_addressId forKey:kAddressId];
            NSData *data = [NSJSONSerialization dataWithJSONObject:dict_localAddress options:NSJSONWritingPrettyPrinted error:nil];
            NSString *str_localAddress = [[NSString alloc] initWithData:data encoding:NSUTF8StringEncoding];
            [editView.dict_updateProfileData setSafeObject:str_localAddress forKey:kLocalAddress];
            DDLogInfo(@"dict is %@",editView.dict_updateProfileData);
            [self.navigationController popViewControllerAnimated:YES];
  
        //}
     }
        
    }
    else
    {
        if ([self checkUserData]) {
            RegistrationViewController *registrationView =  self.navigationController.viewControllers[1];
            NSMutableDictionary *dict_localAddress = [[NSMutableDictionary alloc]init];
            [dict_localAddress setSafeObject:txt_hotel_name.text forKey:kHotelName];
            [dict_localAddress setSafeObject:txt_crossStreet.text forKey:kCrossStreet];
            [dict_localAddress setSafeObject:txt_street_address.text forKey:kStreetAddress];
            [dict_localAddress setSafeObject:txt_city.text forKey:kCity];
            [dict_localAddress setSafeObject:str_stateId forKey:kState];
            [dict_localAddress setSafeObject:txt_state.text forKey:@"stateName"];
            [dict_localAddress setSafeObject:txt_zip.text forKey:kZip];
            [dict_localAddress setSafeObject:klocal forKey:kAddressType];
            NSData *data = [NSJSONSerialization dataWithJSONObject:dict_localAddress options:NSJSONWritingPrettyPrinted error:nil];
            NSString *str_localAddress = [[NSString alloc] initWithData:data encoding:NSUTF8StringEncoding];
            [registrationView.dict_loginData setSafeObject:str_localAddress forKey:kLocalAddress];
            DDLogInfo(@"dict is %@",registrationView.dict_loginData);
            [self.navigationController popViewControllerAnimated:YES];
        }
    }
}
-(BOOL)checkUserData
{
    BOOL isvalid = NO;
    txt_state.text = trimedString(txt_state.text);
    txt_zip.text = trimedString(txt_zip.text);
    txt_street_address.text = trimedString(txt_street_address.text);
    txt_city.text = trimedString(txt_city.text);
    if (txt_street_address==nil|| [txt_street_address.text isEqualToString:@""]) {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterStreetAddress];
        isvalid=NO;
    }
    else if (txt_city.text==nil|| [txt_city.text isEqualToString:@""])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterCity];
        isvalid = NO;
    }
    else if (txt_state.text==nil|| [txt_state.text isEqualToString:@""])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterState];
        isvalid = NO;
    }
    else if (![txt_zip.text isEqualToString:@""])
    {
        if (![[ValidationManager getInstance]validateZip:txt_zip.text])
        {
            [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterValidzip];
            isvalid = NO;
        }else{
            isvalid=YES;
        }
    }
    else
    {
        isvalid = YES;
    }
    return isvalid;
}
-(void)setDataInFields
{
    if (dict_profileData.count > 0) {
        checkValue = 1;
        txt_hotel_name.text = self.parentInfo.HotelName;
        txt_crossStreet.text = self.parentInfo.CrossStreet;
        txt_street_address.text = self.parentInfo.StreetAddress;
        txt_city.text = self.parentInfo.City;
        txt_state.text = self.parentInfo.State;
        txt_zip.text = self.parentInfo.zipCode;
        str_stateId = self.parentInfo.stateID;
    }
    if (self.dict_savedLocalData.count>0) {
        NSString *str_localAddress = [self.dict_savedLocalData safeObjectForKey:kLocalAddress];
        NSError *jsonError;
        NSData *objectData = [str_localAddress dataUsingEncoding:NSUTF8StringEncoding];
        NSDictionary *json = [NSJSONSerialization JSONObjectWithData:objectData
                                                             options:NSJSONReadingMutableContainers
                                                               error:&jsonError];
        
        if ([json allKeys].count >0 ) {
            DDLogInfo(@"Local Address is %@",json);
            
            txt_hotel_name.text = [json safeObjectForKey:kHotelName];
            txt_crossStreet.text = [json safeObjectForKey:kCrossStreet];
            txt_street_address.text = [json safeObjectForKey:kStreetAddress];
            txt_city.text = [json safeObjectForKey:kCity];
            txt_state.text = [json safeObjectForKey:@"stateName"];
            str_stateId = [json safeObjectForKey:kState];
            str_addressId = [json safeObjectForKey:kAddressId];
            txt_zip.text = [json safeObjectForKey:kZip];
            checkValue = 1;
        }
        
    }
    if (self.dict_loginData.count>0) {
        NSString *str_localAddress = [self.dict_loginData safeObjectForKey:kLocalAddress];
        NSError *jsonError;
        NSData *objectData = [str_localAddress dataUsingEncoding:NSUTF8StringEncoding];
        NSDictionary *json = [NSJSONSerialization JSONObjectWithData:objectData
                                                             options:NSJSONReadingMutableContainers
                                                               error:&jsonError];
        
        if ([json allKeys].count >0 ) {
            DDLogInfo(@"Local Address is %@",json);
            
            txt_hotel_name.text = [json safeObjectForKey:kHotelName];
            txt_crossStreet.text = [json safeObjectForKey:kCrossStreet];
            txt_street_address.text = [json safeObjectForKey:kStreetAddress];
            txt_city.text = [json safeObjectForKey:kCity];
            txt_state.text = [json safeObjectForKey:@"stateName"];
            str_stateId = [json safeObjectForKey:kState];
            txt_zip.text = [json safeObjectForKey:kZip];
        }
    }
}

- (IBAction)onClickStateList:(id)sender {
    [self.view endEditing:YES];
    tbl_stateList.hidden = false;
    if (checkDropState == 0) {
        [UIView beginAnimations:@"view_state" context: nil];
        [UIView setAnimationBeginsFromCurrentState:YES];
        [UIView setAnimationDuration:0.25];
        [tbl_stateList setFrame:CGRectMake(txt_state.frame.origin.x, txt_state.frame.origin.y+30,txt_state.frame.size.width,175)];
        [UIView commitAnimations ];
        checkDropState = 1;
    }else{
        [UIView beginAnimations:@"view_state" context: nil];
        [UIView setAnimationBeginsFromCurrentState:YES];
        [UIView setAnimationDuration:0.25];
        [tbl_stateList setFrame:CGRectMake(btn_selectState.frame.origin.x, btn_selectState.frame.origin.y+30,btn_selectState.frame.size.width,0)];
        [UIView commitAnimations ];
        checkDropState = 0;
        
    }
}
- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section;
{
    return array_statelist.count;
}
- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath;
{
    
    static NSString *simpleTableIdentifier = @"SimpleTableItem";
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:simpleTableIdentifier];
    if (cell == nil) {
        cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleValue1 reuseIdentifier:simpleTableIdentifier];
    }
    [cell.textLabel setFont:[UIFont systemFontOfSize:14]];
    cell.textLabel.text = [[array_statelist objectAtIndex:indexPath.row]safeObjectForKey:kState];
    return cell;
}

-(void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath{
    [tableView deselectRowAtIndexPath:indexPath animated:YES];
    txt_state.text = [[array_statelist objectAtIndex:indexPath.row]safeObjectForKey:kState];
    str_stateId = [[array_statelist objectAtIndex:indexPath.row]safeObjectForKey:kStateId];
    
    [UIView beginAnimations:@"table View" context: nil];
    [UIView setAnimationBeginsFromCurrentState:YES];
    [UIView setAnimationDuration:0.25];
    [tbl_stateList setFrame:CGRectMake(txt_state.frame.origin.x, txt_state.frame.origin.y+30,txt_state.frame.size.width,0)];
    [UIView commitAnimations ];
    checkDropState = 0;
    [txt_zip becomeFirstResponder];
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
                
                array_statelist = [[dict_responseObj safeObjectForKey:kData]safeObjectForKey:kStateList];
                [ApplicationManager getInstance].array_stateList = [array_statelist mutableCopy];
                DDLogInfo(@"State list is %@",array_statelist);
                [tbl_stateList reloadData];
                
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
    [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[error localizedDescription] ];
    
}

@end
