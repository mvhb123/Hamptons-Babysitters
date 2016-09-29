//
//  PaymentViewController.m
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 17/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "PaymentViewController.h"
#import "CompleteOrderViewController.h"
#import "ValidationManager.h"
@interface PaymentViewController ()

@end
@implementation PaymentViewController
@synthesize dict_addJobRequirement;
- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.

    NavigationBarRightButton;
    tbl_stateList.hidden = true;
    tbl_stateList.layer.borderWidth = 0.5;
    tbl_stateList.layer.borderColor = [UIColor grayColor].CGColor;
    self.parentInfo = [ApplicationManager getInstance].parentInfo;
    btn_saveEditCard.enabled = false;
    self.navigationItem.rightBarButtonItem.enabled=false;

    if (self.CheckValue == 1) {
        btn_confirmCreditCard.hidden = true;
        lbl_confirmCard.hidden = true;
        [btn_saveCard setSelected:YES];
        str_saveCard = @"1";
        btn_saveEditCard.enabled = true;
        self.navigationItem.rightBarButtonItem.enabled=true;
        lbl_streetAddress.text=@"Street Address";
        lbl_city.text=@"City";
        lbl_state.text=@"State";
        lbl_zip.text=@"Zip";

        [self callGetSavedCardAPI];
    }
    self.navigationItem.title = @"Credit Card Details";
    [btn_saveCard setImage:[UIImage imageNamed:@"Checked"] forState:UIControlStateSelected];
    [btn_saveCard setImage:[UIImage imageNamed:@"Check"] forState:UIControlStateNormal];
    [btn_saveAddress setImage:[UIImage imageNamed:@"Checked"] forState:UIControlStateSelected];
    [btn_saveAddress setImage:[UIImage imageNamed:@"Check"] forState:UIControlStateNormal];
    DDLogInfo(@"dict is %@",self.dict_addJobRequirement);
    str_saveCard = @"0";
    array_statelist = [ApplicationManager getInstance].array_stateList;
    if (array_statelist.count > 0) {
        DDLogInfo(@"State list is %@",array_statelist);
        [tbl_stateList reloadData];
    }
    [self setFontTypeAndFontSize];
    self.view.backgroundColor=kViewBackGroundColor;
    
    
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}
-(void)viewDidAppear:(BOOL)animated
{
    [super viewDidAppear:animated];
    
}
-(void)viewDidLayoutSubviews
{
    [super viewDidLayoutSubviews];
    contentHight = 0;
    UIView *lLast = lbl_confirmCard;//[view_payment.subviews lastObject];
    NSInteger wd = lLast.frame.origin.y;
    NSInteger ht = lLast.frame.size.height;
    contentHight = wd+ht;
    if (lbl_confirmCard.hidden == true) {
        self.backgroundScrollView.contentSize = CGSizeMake(self.backgroundScrollView.frame.size.width,contentHight+10);
        
    }
    else
        self.backgroundScrollView.contentSize = CGSizeMake(self.backgroundScrollView.frame.size.width,contentHight+10);
    self.backgroundScrollView.scrollEnabled = YES;
}
-(void)callGetSavedCardAPI
{
    NSDictionary *dict_savedCardData = @{kToken:self.parentInfo.tokenData,kUserId:self.parentInfo.parentUserId,kAPI_Key:kAPI_KeyValue};
    
    [self startNetworkActivity:NO];
    SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kGetSavedCardAPI];
    DDLogInfo(@"%@",kGetSavedCardAPI);
    networkManager.delegate = self;
    [networkManager getSavedCard:dict_savedCardData forRequestNumber:1];
    
    
    
}
- (void)onTouchOnBackground:(UITapGestureRecognizer*)sender {
    [self.view endEditing:YES];
    // [self.backgroundScrollView setContentOffset:CGPointMake(self.backgroundScrollView.contentOffset.x,0) animated:YES];
}
- (BOOL)textField:(UITextField *)textField shouldChangeCharactersInRange:(NSRange)range replacementString:(NSString *)string {
    if (textField == txt_expiryYear || textField==txt_expiryMonth) {
        if(range.length + range.location > textField.text.length)
        {
            return NO;
        }
        
        NSUInteger newLength = [textField.text length] + [string length] - range.length;
        return newLength <= 2;
        
    }
    return YES;
}
- (void)textFieldDidBeginEditing:(UITextField *)textField{
    checkDropState=0;
    if (textField == txt_state) {
        [self performSelector:@selector(onClickStateList:) withObject:btn_stateList afterDelay:0.1];
        
    }else{
        tbl_stateList.hidden = true;
        
    }
}

- (BOOL)textFieldShouldReturn:(UITextField *)textField{
    [self.view endEditing:YES];
    
    if ((textField!=txt_cvvNumber)&&(textField!=txt_zip)){
        [[self.view viewWithTag:textField.tag+1] becomeFirstResponder];
        
    }
    
    return [textField resignFirstResponder];
}
-(void)setFontTypeAndFontSize
{
    lbl_nameOnCard.font=[UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_cardNumber.font=[UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_expiryDate.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_cvv.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_saveCard.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_EnterBillingAddress.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_billingAddress.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_streetAddress.font=[UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_city.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_state.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_zip.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    
    [lbl_nameOnCard setTextColor:kLabelColor];
    [lbl_cardNumber setTextColor:kLabelColor];
    [lbl_expiryDate setTextColor:kLabelColor];
    [lbl_cvv setTextColor:kLabelColor];
    [lbl_saveCard setTextColor:kLabelColor];
    [lbl_billingAddress setTextColor:kLabelColor];
    [lbl_EnterBillingAddress setTextColor:kLabelColor];
    [lbl_streetAddress setTextColor:kLabelColor];
    [lbl_city setTextColor:kLabelColor];
    [lbl_state setTextColor:kLabelColor];
    [lbl_zip setTextColor:kLabelColor];
}
-(void)saveAction:(UIBarButtonItem *)sender{
    [self onClickSaveEditCardDetail:sender];
}
- (IBAction)onClickCheckSaveCreditCard:(id)sender {
    UIButton *btn=(UIButton*)sender;
    btn.selected=!btn.selected;
    if (btn.selected) {
        str_saveCard =@"1";
        btn_saveEditCard.enabled = true;
        self.navigationItem.rightBarButtonItem.enabled=true;
    }
    else
    {
        btn_saveEditCard.enabled = false;
        str_saveCard = @"0";
        self.navigationItem.rightBarButtonItem.enabled=false;
    }
    
}
- (IBAction)onClickConfirmCreditCard:(id)sender {
    [self.view endEditing:YES];
   // if ([str_cardInfo isEqualToString:@""]|| str_cardInfo == nil) {
    if ([self checkCardDetail]) {
        if ([self checkAddress])
        {
            if ([str_cardInfo isEqualToString:@""]|| str_cardInfo == nil) {
                NSString *str_expiryMonth = txt_expiryMonth.text;
                NSUInteger newLength = [txt_expiryMonth.text length];
                if (newLength == 1) {
                    str_expiryMonth = [NSString stringWithFormat:@"0%@",txt_expiryMonth.text];
                }
                
                NSDictionary *dict_creditCardDetail = @{kCno:txt_cardNumber.text,kCmon:str_expiryMonth,kCyear:txt_expiryYear.text,kCvno:txt_cvvNumber.text};
                str_cardInfo = [[ValidationManager getInstance]encodeCreditCard:dict_creditCardDetail];
                DDLogInfo(@"increapeted credit card %@",str_cardInfo);
                str_nameOnCard = txt_nameOnCard.text;
                DDLogInfo(@"Name on Card %@",str_nameOnCard);

            }
            
            [dict_addJobRequirement setObject:str_saveCard forKey:kSaveCard];
            [dict_addJobRequirement setSafeObject:txt_streetAddress.text forKey:kStreetAddress];
            [dict_addJobRequirement setSafeObject:txt_city.text forKey:kCity];
            [dict_addJobRequirement setSafeObject:txt_state.text forKey:kState];
            [dict_addJobRequirement setSafeObject:txt_zip.text forKey:kZip];
            [dict_addJobRequirement setSafeObject:str_cardInfo forKey:kCardInfo];
            [dict_addJobRequirement setSafeObject:str_nameOnCard forKey:kNameOnCard];
            CompleteOrderViewController *completeCredit = [[CompleteOrderViewController alloc]initWithNibName:@"CompleteOrderViewController" bundle:nil];
            completeCredit.dict_addJobRequirement = [dict_addJobRequirement mutableCopy];
            [self.navigationController pushViewController:completeCredit animated:YES];
      }
  //  }
   
        
    }
}
- (IBAction)onClickCheckjobAddressToBillingAddress:(id)sender {
    UIButton *btn=(UIButton*)sender;
    btn.selected=!btn.selected;
    if (btn.selected) {
        [self enableDisableAddressFields:NO];
        tbl_stateList.hidden= true;
        if (self.CheckValue == 1) {
            btn_stateList.hidden = true;
            txt_streetAddress.text = self.parentInfo.StreetAddress;
            txt_city.text = self.parentInfo.City;
            txt_state.text = self.parentInfo.State;
            txt_zip.text = self.parentInfo.zipCode;
            
        }
        else{
            btn_stateList.hidden = true;
            //txt_crossStreet.text = [[dict_addJobRequirement objectForKey:kLocalAddress]objectForKey:kHotelName];
            txt_streetAddress.text =[[dict_addJobRequirement objectForKey:kLocalAddress]objectForKey:kStreetAddress];
            txt_city.text = [[dict_addJobRequirement objectForKey:kLocalAddress]objectForKey:kCity];
            txt_state.text = [[dict_addJobRequirement objectForKey:kLocalAddress]objectForKey:kState];
            txt_zip.text = [[dict_addJobRequirement objectForKey:kLocalAddress]objectForKey:kZip];
        }
    }
    else
    {
        [self enableDisableAddressFields:YES];
        txt_streetAddress.text = @"";
        txt_crossStreet.text = @"";
        txt_city.text = @"";
        txt_state.text = @"";
        txt_zip.text = @"";
        btn_stateList.hidden = false;
        //tbl_stateList.hidden = false;
        
    }
    
}
- (IBAction)onClickSaveEditCardDetail:(id)sender{
    [self.view endEditing:YES];
    if ([self checkCardDetail]) {
        NSString *str_expiryMonth = txt_expiryMonth.text;
        NSUInteger newLength = [txt_expiryMonth.text length];
        if (newLength == 1) {
            str_expiryMonth = [NSString stringWithFormat:@"0%@",txt_expiryMonth.text];
        }
        
        NSDictionary *dict_creditCardDetail = @{kCno:txt_cardNumber.text,kCmon:str_expiryMonth,kCyear:txt_expiryYear.text,kCvno:txt_cvvNumber.text};
        str_cardInfo = [[ValidationManager getInstance]encodeCreditCard:dict_creditCardDetail];
        DDLogInfo(@"increapeted credit card %@",str_cardInfo);
        str_nameOnCard = txt_nameOnCard.text;
        DDLogInfo(@"Name on Card %@",str_nameOnCard);
        
        if (self.CheckValue == 1) {
            NSDictionary *dict_addEditCardDetail = @{kToken:self.parentInfo.tokenData,kUserId:self.parentInfo.parentUserId,kAPI_Key:kAPI_KeyValue,kNameOnCard:txt_nameOnCard.text,kCardInfo:str_cardInfo,kStreetAddress:txt_streetAddress.text,kCity:txt_city.text,kState:txt_state.text,kZip:txt_zip.text};
            [self startNetworkActivity:NO];
            SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kAddEditCardDetail];
            DDLogInfo(@"%@",kAddEditCardDetail);
            networkManager.delegate = self;
            [networkManager addEditCardDetail:dict_addEditCardDetail forRequestNumber:2];
            
        }
        else
        {
            NSDictionary *dict_addEditCardDetail = @{kToken:self.parentInfo.tokenData,kUserId:self.parentInfo.parentUserId,kAPI_Key:kAPI_KeyValue,kNameOnCard:txt_nameOnCard.text,kCardInfo:str_cardInfo,kStreetAddress:txt_streetAddress.text,kCity:txt_city.text,kState:txt_state.text,kZip:txt_zip.text};
            [self startNetworkActivity:NO];
            SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kAddEditCardDetail];
            DDLogInfo(@"%@",kAddEditCardDetail);
            networkManager.delegate = self;
            [networkManager addEditCardDetail:dict_addEditCardDetail forRequestNumber:2];

           // [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kCardDetailSaved];
        }
    }
}

- (IBAction)onClickStateList:(id)sender {
    [self.view endEditing:YES];
    tbl_stateList.hidden = false;
    if (checkDropState == 0) {
        [self.backgroundScrollView removeGestureRecognizer:self.tapRecognizer];
       // [view_payment bringSubviewToFront:tbl_stateList];
        [UIView beginAnimations:@"view_state" context: nil];
        [UIView setAnimationBeginsFromCurrentState:YES];
        [UIView setAnimationDuration:0.25];
        [tbl_stateList setFrame:CGRectMake(txt_state.frame.origin.x,txt_state.frame.origin.y+30,txt_state.frame.size.width,130)];
        [UIView commitAnimations ];
        checkDropState = 1;
    }else{
        [self.backgroundScrollView addGestureRecognizer:self.tapRecognizer];
        [UIView beginAnimations:@"view_state" context: nil];
        [UIView setAnimationBeginsFromCurrentState:YES];
        [UIView setAnimationDuration:0.25];
        [tbl_stateList setFrame:CGRectMake(txt_state.frame.origin.x, txt_state.frame.origin.y+30,txt_state.frame.size.width,0)];
        [UIView commitAnimations ];
        checkDropState = 0;
        
    }
    
}
-(BOOL)checkCardDetail
{
    BOOL isvalid = YES;
    txt_cvvNumber.text = trimedString(txt_cvvNumber.text);
    txt_expiryMonth.text = trimedString(txt_expiryMonth.text);
    txt_expiryYear.text = trimedString(txt_expiryYear.text);
    NSDateFormatter *formatter = [[NSDateFormatter alloc] init];
    [formatter setDateFormat:@"yy"];
    NSDateFormatter *formatter1 = [[NSDateFormatter alloc] init];
    [formatter1 setDateFormat:@"MM"];
    NSString *yearString = [formatter stringFromDate:[NSDate date]];
    NSString *MonthString = [formatter1 stringFromDate:[NSDate date]];
    int currentYear = [yearString intValue];
    int currentMonth = [MonthString intValue];
    
    if (txt_nameOnCard==nil|| [txt_nameOnCard.text isEqualToString:@""]) {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterNameOnCard];
        isvalid=NO;
    }
    else if (txt_cardNumber==nil || [txt_cardNumber.text isEqualToString:@""])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterCardNumber];
        isvalid=NO;
    }
    
    else if (txt_expiryMonth==nil || [txt_expiryMonth.text isEqualToString:@""])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterExpiryMonth];
        isvalid=NO;
    }
    else if([txt_expiryMonth.text integerValue]>12)
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterValidMonth];
        isvalid=NO;
    }
    else if (currentYear >=[txt_expiryYear.text intValue] && [txt_expiryMonth.text intValue] < currentMonth)
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kCheckExpiryMonth];
        isvalid=NO;
        
    }
    else if (txt_expiryYear==nil || [txt_expiryYear.text isEqualToString:@""])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterExpiryYear];
        isvalid=NO;
    }
    else if ([txt_expiryYear.text intValue] < currentYear)
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kCheckExpiryYear];
        isvalid=NO;
    }
    else if (txt_cvvNumber==nil || [txt_cvvNumber.text isEqualToString:@""])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterCVVNumber];
        isvalid=NO;
        
    }
    else if (![[ValidationManager getInstance]isValidCreditcard:txt_cardNumber.text])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterValidCardNumber];
        isvalid=NO;
    }
    else
    {
        isvalid = YES;
    }
    return isvalid;
}
-(BOOL)checkAddress
{
    BOOL isvalid = NO;
    txt_state.text = trimedString(txt_state.text);
    txt_zip.text = trimedString(txt_zip.text);
    txt_streetAddress.text = trimedString(txt_streetAddress.text);
    txt_city.text = trimedString(txt_city.text);
    if (txt_streetAddress==nil|| [txt_streetAddress.text isEqualToString:@""]) {
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
    else if (txt_zip.text==nil|| [txt_zip.text isEqualToString:@""])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterZipcode];
        isvalid = NO;
    }
    else if (![[ValidationManager getInstance]validateZip:txt_zip.text])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterValidzip];
        isvalid = NO;
    }
    else
    {
        isvalid = YES;
    }
    return isvalid;
}
- (void)enableDisableAddressFields:(BOOL)returnType
{
    [txt_streetAddress setEnabled:returnType];
    [txt_crossStreet setEnabled:returnType];
    [txt_city setEnabled:returnType];
    [txt_state setEnabled:returnType];
    [txt_zip setEnabled:YES];
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
    //[view_payment sendSubviewToBack:tbl_stateList];
    [self.backgroundScrollView addGestureRecognizer:self.tapRecognizer];
}

- (void)requestDidSucceedWithResponseObject:(id)responseObject
                                   withTask:(NSURLSessionDataTask *)task
                              withRequestId:(NSUInteger)requestId{
    NSDictionary *dict_responseObj=responseObject;
    [self stopNetworkActivity];
    DDLogInfo(@"%@",responseObject);
    switch (requestId) {
        case 1:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
                [ApplicationManager getInstance].parentInfo.tokenData = [[responseObject safeObjectForKey:kData]safeObjectForKey:kTokenData];
                [ApplicationManager getInstance].parentInfo.authrizedPaymentProfileId = [[[[responseObject safeObjectForKey:kData]safeObjectForKey:kCardList]safeObjectAtIndex:0]safeObjectForKey:kAutherizePaymentId];
                txt_cardNumber.text =[NSString stringWithFormat:@"%@",[[[[responseObject safeObjectForKey:kData]safeObjectForKey:kCardList]safeObjectAtIndex:0]safeObjectForKey:kCno]];
                txt_nameOnCard.text = [[[[responseObject safeObjectForKey:kData]safeObjectForKey:kCardList]safeObjectAtIndex:0]safeObjectForKey:kNameOnCard];
            }
            else
            {
                
                //   [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
                
            }
            break;
        case 2:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kMessage]];
                [ApplicationManager getInstance].parentInfo.tokenData = [[responseObject safeObjectForKey:kData]safeObjectForKey:kTokenData];
                [ApplicationManager getInstance].parentInfo.authrizedPaymentProfileId = [[[responseObject safeObjectForKey:kData]safeObjectForKey:kCardDetail]safeObjectForKey:kAutherizePaymentId];
                if (self.CheckValue == 1) {
                     [self.navigationController popViewControllerAnimated:YES];
                }
               
                
                
            }
            else
            {
                
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
                
            }
            break;
            
        case 6:// for logout
            [self logout:dict_responseObj];
            break;
            
            
    }
}
- (void)requestDidFailWithErrorObject:(id)error withRequestId:(NSUInteger)requestId{
    [self stopNetworkActivity];
    [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[error localizedDescription] ];
    
}
@end
