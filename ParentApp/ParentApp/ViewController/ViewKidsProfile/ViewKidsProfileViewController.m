//
//  ViewKidsProfileViewController.m
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 08/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "ViewKidsProfileViewController.h"
#import "KidsProfileViewController.h"
#import "ChildProfileView.h"

@interface ViewKidsProfileViewController ()

@end

@implementation ViewKidsProfileViewController

@synthesize dict_childRecord;

- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    array_childrenDetail = [[NSMutableArray alloc]init];
    array_childData = [[NSMutableArray alloc]init];
    scrollView.pagingEnabled = YES;
    [scrollView setShowsVerticalScrollIndicator:YES];
    [scrollView setShowsHorizontalScrollIndicator:YES];
    self.navigationItem.title = @"Child Profiles";
    self.parentInfo = [ApplicationManager getInstance].parentInfo;
    
    self.view.backgroundColor=kViewBackGroundColor;
    self.backgroundScrollView.backgroundColor = kViewBackGroundColor;
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(receiveEvent:) name:@"Add/Update Child"object:nil];
    UILabel *titleLabel = [[UILabel alloc] init];
    titleLabel.text = @"Child Profiles";
    [titleLabel setTextColor:[UIColor whiteColor]];
    [titleLabel sizeToFit];
    self.navigationItem.titleView = titleLabel;
}
-(void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:YES];
    
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}
-(void)viewDidAppear:(BOOL)animated
{  [super viewDidAppear:YES];
    [self callAPI];
}

- (void)receiveEvent:(NSNotification *)notification {
    [self callAPI];
}
-(void) callAPI
{
    NSMutableDictionary *dict_kidsProfile=[[NSMutableDictionary alloc] init];
    [dict_kidsProfile setSafeObject:self.parentInfo.tokenData forKey:kToken];
    [dict_kidsProfile setSafeObject:self.parentInfo.parentUserId forKey:kUserId];
    [dict_kidsProfile setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
    [self startNetworkActivity:YES];
    
    SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kChildrenListApi];
    NSLog(@"%@",kChildrenListApi);
    networkManager.delegate = self;
    [networkManager childrenList:dict_kidsProfile forRequestNumber:1];
    
    
}
- (IBAction)onClickEditChildProfile:(id)sender {
    UIButton *btn=(UIButton*)sender;
    childrenInfo = [array_childrenDetail safeObjectAtIndex:btn.tag];
    
    KidsProfileViewController *kidsProfile = [[KidsProfileViewController alloc]initWithNibName:@"KidsProfileViewController" bundle:nil];
    kidsProfile.childrenInfo = childrenInfo;
    kidsProfile.checkValue = 2;
    [self.navigationController pushViewController:kidsProfile animated:YES];
}
- (void)onClick_NextPage:(id)sender {
    if (pageNo<totalPage) {
        pageNo++;
        [scrollView setContentOffset:CGPointMake(scrollView.frame.size.width*pageNo, scrollView.contentOffset.y) animated:YES];
    }
}
- (void)onClick_PreviousPage:(id)sender
{
    pageNo--;
    [scrollView setContentOffset:CGPointMake(scrollView.frame.size.width*pageNo, scrollView.contentOffset.y) animated:YES];

}
- (IBAction)onClickAddChild:(id)sender {
    KidsProfileViewController *kidsProfile = [[KidsProfileViewController alloc]initWithNibName:@"KidsProfileViewController" bundle:nil];
    kidsProfile.checkValue = 1;
    [self.navigationController pushViewController:kidsProfile animated:YES];
}
- (void)scrollViewDidScroll:(UIScrollView *)scrolView {
    if (((int)scrollView.contentOffset.x % (int)scrollView.frame.size.width) == 0) {
        currentPage = scrollView.contentOffset.x /scrollView.frame.size.width;
        pageNo = currentPage;
    }
}
-(void)setDataInView
{
    DDLogInfo(@"array children detail %@",array_childrenDetail);
    array_childrenDetail = [[NSMutableArray alloc]init];
    DDLogInfo(@"array is %@",[ApplicationManager getInstance].array_childRecord);
    
    array_childrenDetail = [ApplicationManager getInstance].array_childRecord;
    
    NSArray *viewsToRemove = [scrollView subviews];
    for (ChildProfileView *v in viewsToRemove)
        [v removeFromSuperview];
    int j = 0;
    for (int i=0; i<array_childrenDetail.count; i++)
    {
        childrenInfo = [array_childrenDetail safeObjectAtIndex:i];
        ChildProfileView *viewchildProfile;
        NSArray *nibArray = [[NSBundle mainBundle] loadNibNamed:@"ChildProfileView" owner:self options:nil];
        viewchildProfile = [nibArray safeObjectAtIndex:0];
        viewchildProfile.backgroundColor = kViewBackGroundColor;
        
        [viewchildProfile setFrame:CGRectMake(j,0, scrollView.frame.size.width, scrollView.frame.size.height)];
        [viewchildProfile.view_childImage setFrame:CGRectMake(viewchildProfile.view_childImage.frame.origin.x, viewchildProfile.view_childImage.frame.origin.y, 100, 100)];

        viewchildProfile.lbl_childName.text = childrenInfo.childName;
        viewchildProfile.lbl_ageValue.text = trimedString(childrenInfo.childAge);
        if (![childrenInfo.childRelationShip isEqualToString:@"Parent"]&&![childrenInfo.childRelationShip isEqualToString:@"Legal guardian"]) {
            viewchildProfile.lbl_relationShip.text=childrenInfo.childRelationShip;
            viewchildProfile.lbl_parentName.text=childrenInfo.childParentName;
            viewchildProfile.lbl_parentContact.text=childrenInfo.childParentContact;
            viewchildProfile.consForAllergies.constant=10;
        }else{
            viewchildProfile.consForAllergies.constant=-40;
            viewchildProfile.lbl_relationShip.text=childrenInfo.childRelationShip;
            viewchildProfile.lbl_parentName.hidden=YES;
            viewchildProfile.lbl_parentContact.hidden=YES;
            viewchildProfile.lbl_parentNameHeading.hidden=YES;
            viewchildProfile.lbl_parentContactHeading.hidden=YES;
        }
        if ([childrenInfo.childRelationShip isEqualToString:@""]||childrenInfo.childRelationShip==nil) {
            viewchildProfile.lbl_parentName.hidden=YES;
            viewchildProfile.lbl_parentContact.hidden=YES;
            viewchildProfile.lbl_parentNameHeading.hidden=YES;
            viewchildProfile.lbl_parentContactHeading.hidden=YES;
            viewchildProfile.lbl_relationShip.hidden=YES;
            viewchildProfile.lbl_relationShipHeading.hidden=YES;
           
        }
        
        if ([childrenInfo.childSex isEqualToString:@"M"]) {
            viewchildProfile.lbl_sexValue.text =@"Male";
        }
        else
             viewchildProfile.lbl_sexValue.text =@"Female";
        if ([childrenInfo.childSpecialNeedsStatus isEqualToString:@"Yes"]) {
            viewchildProfile.lbl_specialNeeds.text = childrenInfo.childSpecialNeeds;
        }
        else
        {
            viewchildProfile.lbl_specialNeeds.text = childrenInfo.childSpecialNeedsStatus;
        }
        if ([childrenInfo.childAllergyStatus isEqualToString:@"Yes"]) {
            viewchildProfile.lbl_alergies.text = childrenInfo.childallergies;
        }
        else
        {
            viewchildProfile.lbl_alergies.text = childrenInfo.childAllergyStatus;
        }
        if ([childrenInfo.childMedicatorStatus isEqualToString:@"Yes"]) {
            viewchildProfile.lbl_medications.text = childrenInfo.childMedicator;
        }
        else
        {
            viewchildProfile.lbl_medications.text = childrenInfo.childMedicatorStatus;
        }
        if ([childrenInfo.childHelpFullHint isEqualToString:@""]) {
            [viewchildProfile.lbl_viewHelpFullHint setHidden:YES];
            [viewchildProfile.txtView_specialHints setHidden:YES];
            viewchildProfile.consForFavFood.constant=-100;
           
        }else{
            [viewchildProfile.lbl_viewHelpFullHint setHidden:NO];
            [viewchildProfile.txtView_specialHints setHidden:NO];
            viewchildProfile.txtView_specialHints.text = childrenInfo.childHelpFullHint;
        }
        if ([childrenInfo.childFavbook isEqualToString:@""]) {
            viewchildProfile.lbl_favouriteBook.text=@"Not specified";
        }else{
            viewchildProfile.lbl_favouriteBook.text = childrenInfo.childFavbook;
        }
        if ([childrenInfo.childFavCartoon isEqualToString:@""]) {
            viewchildProfile.lbl_favouriteCartoon.text=@"Not specified";
        }else{
            viewchildProfile.lbl_favouriteCartoon.text = childrenInfo.childFavCartoon;
        }
        if ([childrenInfo.childfavFood isEqualToString:@""]) {
            viewchildProfile.lbl_favouriteFood.text=@"Not specified";
        }else{
            viewchildProfile.lbl_favouriteFood.text = childrenInfo.childfavFood;
        }
         [viewchildProfile setNeedsUpdateConstraints];
       // [viewchildProfile.view_childImage.imageView setContentMode:UIViewContentModeCenter];
        NSURL *img_url = [NSURL URLWithString:childrenInfo.childThumbImage];
        [viewchildProfile.view_childImage loadImageFromURL:img_url];
        viewchildProfile.btn_editProfile.tag = i;
        viewchildProfile.btn_nextPage.tag = i;
        viewchildProfile.btn_previousPage.tag = i;
        [scrollView addSubview:viewchildProfile];
        [viewchildProfile.btn_editProfile addTarget:self action:@selector(onClickEditChildProfile:) forControlEvents:UIControlEventTouchUpInside];
        if (viewchildProfile.btn_nextPage.tag == array_childrenDetail.count-1) {
            viewchildProfile.btn_nextPage.hidden = true;
        }
        if (viewchildProfile.btn_previousPage.tag == 0) {
            viewchildProfile.btn_previousPage.hidden = true;
        }
        [viewchildProfile.btn_nextPage addTarget:self action:@selector(onClick_NextPage:) forControlEvents:UIControlEventTouchUpInside];
        [viewchildProfile.btn_previousPage addTarget:self action:@selector(onClick_PreviousPage:) forControlEvents:UIControlEventTouchUpInside];
        j=j+viewchildProfile.frame.size.width;
        totalPage = i;
        
    }
   
    scrollView.contentSize = CGSizeMake(j,self.backgroundScrollView.frame.size.height);
   // [self.backgroundScrollView setContentSize:CGSizeMake(self.view.frame.size.width, 1000)];
}

#pragma mark - SMCoreNetworkManagerDelegate

- (void)requestDidSucceedWithResponseObject:(id)responseObject
                                   withTask:(NSURLSessionDataTask *)task
                              withRequestId:(NSUInteger)requestId{
    NSDictionary *dict_responseObj=responseObject;
    [self stopNetworkActivity];
    NSLog(@"%@",responseObject);
    switch (requestId) {
        case 1:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
                [array_childData removeAllObjects];
                self.parentInfo.tokenData = [[dict_responseObj safeObjectForKey:kData]safeObjectForKey:kTokenData];
                NSLog(@"all Keys are %@",[[[dict_responseObj safeObjectForKey:kData]objectForKey:kChildList] allKeys]);
                NSArray *tempArray=[[[dict_responseObj safeObjectForKey:kData]safeObjectForKey:kChildList] allKeys];
                for (int i = 0; i <[[[[dict_responseObj safeObjectForKey:kData]safeObjectForKey:kChildList] allKeys]count]; i++) {
                    [array_childData addObject:[[[dict_responseObj safeObjectForKey:kData] safeObjectForKey:kChildList] safeObjectForKey:[tempArray objectAtIndex:i]]];
                }
                
                [[ApplicationManager getInstance]saveChildData:array_childData];
                [self setDataInView];
            }
            
            else
            {
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
            }
            
            
            break;
        case 6:
            [self logout:dict_responseObj];
            break;
            
    }
}
- (void)requestDidFailWithErrorObject:(id)error withRequestId:(NSUInteger)requestId{
    [self stopNetworkActivity];
    [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[error localizedDescription] ];
    
}
#pragma mark -- AlertView delegate
- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex
{
    
    if (alertView.tag==100 && buttonIndex==1) {
        KidsProfileViewController *kidsProfile = [[KidsProfileViewController alloc]initWithNibName:@"KidsProfileViewController" bundle:nil];
        kidsProfile.dict_parentRecord = [dict_childRecord mutableCopy];
        kidsProfile.checkValue = 1;
        [self.navigationController pushViewController:kidsProfile animated:YES];
    }
    [self logoutAlert:alertView clickedButtonAtIndex:buttonIndex];
}

@end
