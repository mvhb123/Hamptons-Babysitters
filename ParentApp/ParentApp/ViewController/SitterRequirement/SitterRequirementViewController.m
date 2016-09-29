//
//  SitterRequirementViewController.m
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 17/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "SitterRequirementViewController.h"
#import "SitterRequirementTableViewCell.h"
#import "RequestSitterViewController.h"

@interface SitterRequirementViewController ()

@end

@implementation SitterRequirementViewController
@synthesize dict_sitterRequirement;
- (void)viewDidLoad {
    [super viewDidLoad];
    array_sitterRequirement = [[NSMutableArray alloc]init];
    array_langauges = [[NSMutableArray alloc]init];
    array_otherPreferences = [[NSMutableArray alloc]init];
    array_selectedPrefrence= [[NSMutableArray alloc]init];
    lbl_bottom.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    btn_save.titleLabel.font = [UIFont fontWithName:RobotoRegularFont size:ButtonFieldFontSize];
    DDLogInfo(@"dict is %@",dict_sitterRequirement);
    self.view.backgroundColor=kViewBackGroundColor;
    // Do any additional setup after loading the view from its nib.
    self.navigationItem.title = @"Sitter Preferences";
    [array_selectedPrefrence addObjectsFromArray:self.array_childPreference];
    array_sitterRequirement = [ApplicationManager getInstance].array_sitterRequirement;
    if (array_sitterRequirement.count>0) {
        DDLogInfo(@"array is %@",array_sitterRequirement);
        for (int i = 0; i<array_sitterRequirement.count; i++) {
            if ([[[array_sitterRequirement objectAtIndex:i]objectForKey:kGroupName]isEqualToString:kLanguage]) {
                [array_langauges addObject:[array_sitterRequirement objectAtIndex:i]];
                
            }
            else
            {
                [array_otherPreferences addObject:[array_sitterRequirement objectAtIndex:i]];
            }
            [tbl_groupName reloadData];
        }
    }
    else{
       [self callSitterRequirementAPI];
    }
    
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

-(void)callSitterRequirementAPI
{
    NSMutableDictionary *dict_sitterRequirementData = [[NSMutableDictionary alloc]init];
    [dict_sitterRequirementData setSafeObject:self.parentInfo.tokenData forKey:kToken];
    [dict_sitterRequirementData setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
    [dict_sitterRequirementData setSafeObject:self.parentInfo.parentUserId forKey:kUserId];
    [self startNetworkActivity:YES];
    SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kSitterRequirementAPI];
    DDLogInfo(@"%@",kSitterRequirementAPI);
    networkManager.delegate = self;
    [networkManager requestSitter:dict_sitterRequirementData forRequestNumber:1];
 
}
- (IBAction)onClickChecked:(id)sender {
    UIButton *btn=(UIButton*)sender;
    btn.selected=!btn.selected;
    self.checkValue = 0;
    DDLogInfo(@"tag is %ld",(long)btn.tag);
    if (btn.selected) {
        [array_selectedPrefrence addObject:[array_langauges objectAtIndex:btn.tag]];
        
    }
    else
    {
        [array_selectedPrefrence removeObject:[array_langauges objectAtIndex:btn.tag]];
        
    }
    DDLogInfo(@"array is %@",array_selectedPrefrence);
}
- (IBAction)onClickChecked1:(id)sender {
    UIButton *btn=(UIButton*)sender;
    btn.selected=!btn.selected;
    self.checkValue=0;
    DDLogInfo(@"tag is %ld",(long)btn.tag);
    if (btn.selected) {
        [array_selectedPrefrence addObject:[array_otherPreferences objectAtIndex:btn.tag]];
        
    }
    else
        [array_selectedPrefrence removeObject:[array_otherPreferences objectAtIndex:btn.tag]];
    
   
    DDLogInfo(@"array is %@",array_selectedPrefrence);
}
- (IBAction)onClickSave:(id)sender {
    NSArray *array = [self.navigationController viewControllers];
    RequestSitterViewController *requestSitter = self.navigationController.viewControllers[[array count]-2];
    requestSitter.array_Preferences = [array_selectedPrefrence mutableCopy];
    [self.navigationController popViewControllerAnimated:YES];
}

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView {
    return 2;
}
//- (NSString *)tableView:(UITableView *)tableView titleForHeaderInSection:(NSInteger)section
//{
//        if (section == 0) {
//            return @"Language";
//        }
//        if (section==1) {
//            return @"Other Preferences";
//        }
//    return nil;
//}
- (UIView *)tableView:(UITableView *)tableView viewForHeaderInSection:(NSInteger)section{
    UIView *viewHeader=[[UIView alloc]initWithFrame:CGRectMake(0, 0,tbl_groupName.frame.size.width,40)];
    viewHeader.backgroundColor= kViewBackGroundColor;
    viewHeader.tag                  = section;
//    UIImageView *imgHeaderBg=[[UIImageView alloc]initWithImage:[UIImage imageNamed:@"accordian.png"]];
//    [imgHeaderBg setFrame:viewHeader.frame];
//    [viewHeader addSubview:imgHeaderBg];

    CGRect lblFrm=viewHeader.frame;
    UILabel *lblHeaderTitle=[[UILabel alloc]initWithFrame:lblFrm];
    NSString *strTitle=@"";
    if (section == 0) {
        strTitle =  @"  Languages";
    }
    if (section==1) {
        strTitle = @"  Other Preferences";
    }
    [lblHeaderTitle setTextColor:UIColorFromHexCode(0x04005c)];
    [lblHeaderTitle setText:strTitle];
    lblHeaderTitle.font = [UIFont fontWithName:RobotoMediumFont size:ButtonFieldFontSize];
    [viewHeader addSubview:lblHeaderTitle];
     
    UILabel *lblLine=[[UILabel alloc]initWithFrame:CGRectMake(10, 38,tbl_groupName.frame.size.width-20,1)];
    [lblLine setBackgroundColor:[UIColor lightGrayColor]];
    [viewHeader addSubview:lblLine];
    return viewHeader;
    
}
// TableView DatoSource delegates methods.
- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
  
    if (array_sitterRequirement.count!=0) {
        if (section==0) {
            return array_langauges.count;
        }
        if (section == 1) {
            return array_otherPreferences.count;
        }
   
    }
    else
        return array_sitterRequirement.count;
    return 0;
}
 //method for set row hight.
- (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath
{
    return 40.0;
}

// tableview data source metod for set the value of rows cells.
- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    static NSString *CellIdentifier =@"TableViewController";
    
    SitterRequirementTableViewCell *cell = (SitterRequirementTableViewCell *)[tbl_groupName dequeueReusableCellWithIdentifier:CellIdentifier];
    if (cell == nil) {
        NSArray *nib = [[NSBundle mainBundle] loadNibNamed:@"SitterRequirementTableViewCell" owner:self options:nil];
        cell = [nib objectAtIndex:0];
        cell.contentView.backgroundColor=kViewBackGroundColor;
        [cell.btn_checked setImage:[UIImage imageNamed:@"Checked"] forState:UIControlStateSelected];
        [cell.btn_checked setImage:[UIImage imageNamed:@"Check"] forState:UIControlStateNormal];
        cell.btn_checked.tag = indexPath.row;
        if (indexPath.section == 0) {
            [cell.btn_checked addTarget:self
                                 action:@selector(onClickChecked:) forControlEvents:UIControlEventTouchUpInside];
        }
        else
        [cell.btn_checked addTarget:self
                            action:@selector(onClickChecked1:) forControlEvents:UIControlEventTouchUpInside];
    }
    cell.selectionStyle = UITableViewCellSelectionStyleNone;
    cell.backgroundColor = [UIColor colorWithRed:239 green:239 blue:244 alpha:0];
    if (array_sitterRequirement.count!=0) {
        
        if (indexPath.section==0) {
            if (self.checkValue ==1) {
                
            
            BOOL checkSeletced = [self checkSelected1:[array_langauges objectAtIndex:indexPath.row]];
            if (checkSeletced) {
                if (checkSeletced) {
                    cell.btn_checked.selected = YES;
                }
                else
                {
                    cell.btn_checked.selected = NO;
                }
 
              }
            }
            else
            {
                
            BOOL check = [self checkSelected:[array_langauges objectAtIndex:indexPath.row]];
            if (check) {
                cell.btn_checked.selected = YES;
            }
            else
            {
                cell.btn_checked.selected = NO;
            }
            }
            cell.lbl_groupName.text = [[array_langauges objectAtIndex:indexPath.row]objectForKey:kPreferName];
            return cell;
        
            
                
        }
        if (indexPath.section==1) {
            if (self.checkValue ==1) {
                
            
            BOOL checkSeletced = [self checkSelected1:[array_otherPreferences objectAtIndex:indexPath.row]];
            if (checkSeletced) {
                if (checkSeletced) {
                    cell.btn_checked.selected = YES;
                }
                else
                {
                    cell.btn_checked.selected = NO;
                }
            }
            }
            else
            {
            BOOL check = [self checkSelected:[array_otherPreferences objectAtIndex:indexPath.row]];
            if (check) {
                cell.btn_checked.selected = YES;
            }
            else
            {
                cell.btn_checked.selected = NO;
            }
            }
            cell.lbl_groupName.text =[[array_otherPreferences objectAtIndex:indexPath.row]objectForKey:kPreferName];
           
            return cell;
        }
    }
    
    return nil;
    
}

-(void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath{
}

-(BOOL)checkSelected:(NSDictionary*)dict
{
    int j = 0;
    DDLogInfo(@"selected array is %@",array_selectedPrefrence);
   for (int i = 0; i<array_selectedPrefrence.count; i++) {
        if ([[[array_selectedPrefrence objectAtIndex:i]objectForKey:kPreferName]isEqualToString:[dict objectForKey:kPreferName]]) {
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
-(BOOL)checkSelected1:(NSDictionary*)dict
{
    int j = 0;
    DDLogInfo(@"selected array is %@",self.array_childPreference);
    for (int i = 0; i<self.array_childPreference.count; i++) {
        if ([[[self.array_childPreference objectAtIndex:i]objectForKey:kPreferName]isEqualToString:[dict objectForKey:kPreferName]]) {
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
                NSUserDefaults *userDefaults = [NSUserDefaults standardUserDefaults];
                
                [userDefaults setObject:[[responseObject objectForKey:kData] objectForKey:kTokenData] forKey:kTokenValue];
                [userDefaults synchronize];
            self.parentInfo.tokenData = [[dict_responseObj objectForKey:kData]safeObjectForKey:kTokenData];
            dict_sitterRequirement = [dict_responseObj mutableCopy];
            array_sitterRequirement = [[dict_sitterRequirement objectForKey:kData]objectForKey:kJobPreferList];
            [ApplicationManager getInstance].array_sitterRequirement = [array_sitterRequirement mutableCopy];
            
            DDLogInfo(@"array is %@",array_sitterRequirement);
             for (int i = 0; i<array_sitterRequirement.count; i++) {
                 if ([[[array_sitterRequirement objectAtIndex:i]objectForKey:kGroupName]isEqualToString:kLanguage]) {
                     [array_langauges addObject:[array_sitterRequirement objectAtIndex:i]];
                     
                 
             }
                 else
                 {
                     [array_otherPreferences addObject:[array_sitterRequirement objectAtIndex:i]];
                 }
                 [tbl_groupName reloadData];
                
            }
            }
            else
            {
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
            }
            
            break;
        case 6://for logout
            [self logout:dict_responseObj];
            break;

            
    }
}
- (void)requestDidFailWithErrorObject:(id)error withRequestId:(NSUInteger)requestId{
    [self stopNetworkActivity];
    [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[error localizedDescription] ];
    
}
@end
