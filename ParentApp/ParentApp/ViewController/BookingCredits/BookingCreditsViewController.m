//
//  BookingCreditsViewController.m
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 17/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "BookingCreditsViewController.h"
#import "BookingCreditsTableViewCell.h"
#import "CompleteOrderViewController.h"

@interface BookingCreditsViewController ()

@end

@implementation BookingCreditsViewController
- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    array_packageList= [[NSMutableArray alloc]init];
    array_addValue =   [[NSMutableArray alloc]init];
    self.navigationItem.title = @"Booking Credits";
    
    lbl_buyBookingHeading.font = [UIFont fontWithName:RobotoRegularFont size:HeadingFieldFontSize];
    lbl_creditsDetail.font = [UIFont fontWithName:RobotoMediumFont size:11.0];
    lbl_availableCredits.font = [UIFont fontWithName:RobotoMediumFont size:11.0];
    btn_addOrder.titleLabel.font = [UIFont fontWithName:RobotoRegularFont size:ButtonFieldFontSize];
    btn_cancel.titleLabel.font = [UIFont fontWithName:RobotoRegularFont size:ButtonFieldFontSize];
    [lbl_buyBookingHeading setTextColor:UIColorFromHexCode(0x04005c)];
    [lbl_creditsDetail setTextColor:kLabelColor];
    [lbl_availableCredits setTextColor:kLabelColor];

    NSMutableDictionary *dict_packageData = [[NSMutableDictionary alloc]init];
    if (self.checkValue==0) {
        [dict_packageData setSafeObject:[ApplicationManager getInstance].str_startDate forKey:kJobStartDate];
    }
    [dict_packageData setSafeObject:self.parentInfo.tokenData forKey:kToken];
    [dict_packageData setSafeObject:self.parentInfo.parentUserId forKey:kUserId];
    [dict_packageData setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
    
    [self startNetworkActivity:YES];
    SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kPackageList];
    DDLogInfo(@"%@",kPackageList);
    networkManager.delegate = self;
    [networkManager packeageList:dict_packageData forRequestNumber:1];
    self.view.backgroundColor=kViewBackGroundColor;
    tbl_PackageList.backgroundColor = kViewBackGroundColor;

}
-(void)viewWillAppear:(BOOL)animated{
    [super viewWillAppear:animated];
    if (self.checkValue==1) {
        [btn_addOrder setTitle:@"Buy Credits" forState:UIControlStateNormal];
    }else{
    [btn_addOrder setTitle:@"Add to Order" forState:UIControlStateNormal];
    }
}
- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (IBAction)onClickCancleBookingCredits:(id)sender {
    [self.navigationController popViewControllerAnimated:YES];
}
- (IBAction)onClickAddToOrderBookingCredits:(id)sender {
    if (self.checkValue == 0) {
        if (array_addValue.count>0) {
        NSArray *array = [self.navigationController viewControllers];
        CompleteOrderViewController *completeOrder =  self.navigationController.viewControllers[[array count]-2];
        [completeOrder.dict_addJobRequirement setSafeObject:[[array_addValue safeObjectAtIndex:0]safeObjectForKey:kPackageId] forKey:kPackageId];
         [self.navigationController popViewControllerAnimated:YES];
        }
        else
            [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:kSelectCredits];
    }
    else
    {
        if (array_addValue.count>0) {
            if ([self.parentInfo.authrizedPaymentProfileId isEqualToString:@""]) {
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:kAddCardDetail];
                [self.navigationController popViewControllerAnimated:YES];
            }
            else
            {
            NSDictionary *dict_bookingCredits = @{kUserId:self.parentInfo.parentUserId,kToken:self.parentInfo.tokenData,kAPI_Key:kAPI_KeyValue,kPackageId:[[array_addValue safeObjectAtIndex:0]safeObjectForKey:kPackageId],kAutherizePaymentId:self.parentInfo.authrizedPaymentProfileId};
            [self startNetworkActivity:YES];
            SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kBuyCreditsAPI];
            DDLogInfo(@"%@",kBuyCreditsAPI);
            networkManager.delegate = self;
            [networkManager buyCredits:dict_bookingCredits forRequestNumber:2];
            }
 
        }
        else
        [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:kSelectCredits];
    }
    
}
// TableView DatoSource delegates methods.
- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    return array_packageList.count;
}
// method for set row hight.
- (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath
{
    return 35.0;
}

// tableview data source metod for set the value of rows cells.
- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{

    static NSString *CellIdentifier =@"TableViewController";
    
    BookingCreditsTableViewCell *cell = (BookingCreditsTableViewCell *)[tbl_PackageList dequeueReusableCellWithIdentifier:CellIdentifier];
    if (cell == nil) {
        NSArray *nib = [[NSBundle mainBundle] loadNibNamed:@"BookingCreditsTableViewCell" owner:self options:nil];
        cell = [nib objectAtIndex:0];
        [cell.btn_checked setImage:[UIImage imageNamed:@"On"] forState:UIControlStateSelected];
        [cell.btn_checked setImage:[UIImage imageNamed:@"Off"] forState:UIControlStateNormal];
        cell.btn_checked.tag = indexPath.row;
        [cell.btn_checked addTarget:self
                                 action:@selector(onClickChecked:) forControlEvents:UIControlEventTouchUpInside];
        
       
    }
    cell.selectionStyle = UITableViewCellSelectionStyleNone;
    cell.backgroundColor = kViewBackGroundColor;
    BOOL check =[self checkSelected:[array_packageList objectAtIndex:indexPath.row]];
    if (check) {
        [cell.btn_checked setSelected:YES];
    }
    else
    [cell.btn_checked setSelected:NO];
    cell.lbl_credits.text =[[array_packageList objectAtIndex:indexPath.row]objectForKey:kCredits];
    cell.lbl_price.text   = [NSString stringWithFormat:@"$%@ %@",[[array_packageList objectAtIndex:indexPath.row]objectForKey:kPrice],[[array_packageList objectAtIndex:indexPath.row]objectForKey:kPackageName]];
    
    return cell;
    
}
 NSInteger checkTag;
- (IBAction)onClickChecked:(id)sender {
   
    UIButton *btn=(UIButton*)sender;
    btn.selected=!btn.selected;
    DDLogInfo(@"tag is %ld",(long)btn.tag);
    if (btn.selected) {
             [array_addValue removeAllObjects];
             [array_addValue addObject:[array_packageList objectAtIndex:btn.tag]];
             [btn setSelected:YES];

    }
     [tbl_PackageList reloadData];
}
-(BOOL)checkSelected:(NSDictionary*)dict
{
    
    int j = 0;
    DDLogInfo(@"selected array is %@",array_addValue);
    for (int i = 0; i<array_addValue.count; i++) {
        if ([[[array_addValue objectAtIndex:i]objectForKey:kOrdering]isEqualToString:[dict objectForKey:kOrdering]]) {
            j=1;
            break;
        }
    }
    if (j==1) {
        return true;
    }
    else
        return false;
    
    return false;
}

-(void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath{
    
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
                NSString *strCredit=[NSString stringWithFormat:@"Available Credits: %ld",(long)[[[responseObject safeObjectForKey:kData]safeObjectForKey:@"available_credits"] integerValue]];
                lbl_availableCredits.text=strCredit;
                array_packageList = [[dict_responseObj objectForKey:kData]objectForKey:kPackageData];
                DDLogInfo(@"Packege list is %@",array_packageList);
                [tbl_PackageList reloadData];
            }
            else
            {
                
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
                
            }
            break;
        case 2:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[dict_responseObj valueForKey:kMessage]];
                [ApplicationManager getInstance].parentInfo.tokenData = [[responseObject safeObjectForKey:kData]safeObjectForKey:kTokenData];
                [self.navigationController popViewControllerAnimated:YES];
               
              
            }
            else
            {
                
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
                
            }
            break;

        case 6:
            [self logout:dict_responseObj];
            break;

    }
}
- (void)requestDidFailWithErrorObject:(id)error withRequestId:(NSUInteger)requestId{
    [self stopNetworkActivity];
   // [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[error localizedDescription] ];
     [self showAlertForSelf:self withTitle:nil andMessage:[error localizedDescription]];
    
}
- (void)showAlertForSelf:(id)vc withTitle:(NSString*)title andMessage:(NSString*)message {
    UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"" message:message delegate:vc cancelButtonTitle:@"OK" otherButtonTitles:nil];
    [alert setTag:1001];
    [alert show];
}
- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex {
    if (alertView.tag == 1001) {
        [self.navigationController popViewControllerAnimated:YES];
    }
     [self logoutAlert:alertView clickedButtonAtIndex:buttonIndex];
}
@end
